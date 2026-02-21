<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\PterodactylService;
use App\Services\ServerProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServerController extends Controller
{
    protected $ptero;

    public function __construct(PterodactylService $ptero)
    {
        $this->ptero = $ptero;
    }

    public function index()
    {
        return Server::with(['user', 'product', 'node'])->orderBy('id', 'desc')->get();
    }

    public function show(Server $server)
    {
        return $server->load(['user', 'product', 'node']);
    }

    public function store(Request $request, ServerProvisioningService $provisioning)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $product = Product::findOrFail($validated['product_id']);

        // Create a zero-amount admin order to link server and payments history if needed
        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => 0,
            'status' => 'admin',
        ]);

        try {
            $server = $provisioning->provision($order);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json($server->load(['user', 'product', 'node']), 201);
    }

    public function update(Request $request, Server $server)
    {
        // Allow updating status or owner manually if needed
        $validated = $request->validate([
            'status' => 'in:active,suspended,cancelled',
            'expires_at' => 'date',
        ]);

        if (isset($validated['status']) && $validated['status'] !== $server->status) {
            // Sync with Ptero
            if ($validated['status'] === 'suspended') {
                try { $this->ptero->suspendServer($server->ptero_server_id); } catch (\Exception $e) {}
            } elseif ($validated['status'] === 'active') {
                try { $this->ptero->unsuspendServer($server->ptero_server_id); } catch (\Exception $e) {}
            }
        }

        $server->update($validated);
        return $server;
    }

    public function destroy(Server $server)
    {
        // Delete from Ptero
        try {
            $this->ptero->deleteServer($server->ptero_server_id);
        } catch (\Exception $e) {
            // Log error but proceed to delete from DB
        }
        
        $server->delete();
        return response()->noContent();
    }

    public function cancel(Request $request, Server $server)
    {
        if ($server->status !== 'active') {
            return response()->json(['error' => 'Server is not active'], 422);
        }
        $user = $server->user;
        $product = $server->product;
        $now = now();
        $expires = $server->expires_at ? \Carbon\Carbon::parse($server->expires_at) : $now;
        $totalDays = max(1, $now->diffInDays($now->copy()->addMonth()));
        $remainingDays = max(0, $now->diffInDays($expires, false));
        $fraction = min(1, max(0, $remainingDays / $totalDays));
        $refund = round($product->price_monthly * $fraction, 2);

        try {
            return DB::transaction(function () use ($server, $user, $refund) {
                if ($refund > 0) {
                    $user->increment('balance', $refund);
                    $user->payments()->create([
                        'amount' => $refund,
                        'gateway' => 'refund',
                        'status' => 'paid',
                    ]);
                }
                try { $this->ptero->deleteServer($server->ptero_server_id); } catch (\Exception $e) {}
                $server->status = 'cancelled';
                $server->expires_at = now();
                $server->save();
                return response()->json(['message' => 'Server cancelled', 'refund' => $refund]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Cancellation failed: ' . $e->getMessage()], 500);
        }
    }

    public function changePlan(Request $request, Server $server)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        $newProduct = Product::findOrFail($request->product_id);
        $oldProduct = $server->product;
        if ($newProduct->id === $oldProduct->id) {
            return response()->json(['error' => 'Selected plan is the same'], 422);
        }
        $now = now();
        $expires = $server->expires_at ? \Carbon\Carbon::parse($server->expires_at) : $now;
        $totalDays = max(1, $now->diffInDays($now->copy()->addMonth()));
        $remainingDays = max(0, $now->diffInDays($expires, false));
        $fraction = min(1, max(0, $remainingDays / $totalDays));
        $diff = round(($newProduct->price_monthly - $oldProduct->price_monthly) * $fraction, 2);

        try {
            return DB::transaction(function () use ($server, $newProduct, $diff) {
                $user = $server->user;
                if ($diff > 0) {
                    if ($user->balance < $diff) {
                        abort(402, 'Insufficient balance for upgrade');
                    }
                    $user->decrement('balance', $diff);
                    $user->payments()->create([
                        'amount' => -$diff,
                        'gateway' => 'balance',
                        'status' => 'paid',
                    ]);
                } elseif ($diff < 0) {
                    $user->increment('balance', abs($diff));
                    $user->payments()->create([
                        'amount' => abs($diff),
                        'gateway' => 'refund',
                        'status' => 'paid',
                    ]);
                }
                $res = $newProduct->resources ?? [];
                $limits = [
                    'memory' => $res['ram'] ?? 1024,
                    'swap' => 0,
                    'disk' => $res['disk'] ?? 10240,
                    'io' => 500,
                    'cpu' => $res['cpu'] ?? 100,
                ];
                $featureLimits = [
                    'databases' => $res['databases'] ?? 0,
                    'allocations' => max(0, ($res['ports'] ?? 1) - 1),
                    'backups' => $res['backups'] ?? 0,
                ];
                try { $this->ptero->updateServerBuild($server->ptero_server_id, $limits, $featureLimits); } catch (\Exception $e) {}
                $server->product_id = $newProduct->id;
                $server->save();
                return response()->json(['message' => 'Plan changed', 'difference' => $diff]);
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Change plan failed: ' . $e->getMessage()], 500);
        }
    }
}
