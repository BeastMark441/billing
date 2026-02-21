<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Services\PterodactylService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServerController extends Controller
{
    protected $ptero;

    public function __construct(PterodactylService $ptero)
    {
        $this->ptero = $ptero;
    }

    public function index(Request $request)
    {
        return $request->user()->servers()->with('product', 'node')->get();
    }
    
    public function show(Request $request, $id)
    {
        return $request->user()->servers()->with('product', 'node')->findOrFail($id);
    }

    public function resources(Request $request, Server $server)
    {
        if ($request->user()->id !== $server->user_id) {
            abort(403);
        }

        if (!$server->identifier) {
             // Fallback: Try to fetch from Ptero using ID if identifier is missing (legacy)
             try {
                $details = $this->ptero->getServerDetails($server->ptero_server_id);
                $server->identifier = $details['identifier'];
                $server->save();
             } catch (\Exception $e) {
                 return response()->json(['error' => 'Server not found on Pterodactyl'], 404);
             }
        }

        $resources = $this->ptero->getServerResources($server->identifier);
        
        if (!$resources) {
             return response()->json(['state' => 'offline'], 200);
        }

        return response()->json($resources);
    }

    public function power(Request $request, Server $server)
    {
        if ($request->user()->id !== $server->user_id) {
            abort(403);
        }

        $request->validate([
            'signal' => 'required|in:start,stop,restart,kill'
        ]);

        try {
            if (!$server->identifier) {
                 $details = $this->ptero->getServerDetails($server->ptero_server_id);
                 $server->identifier = $details['identifier'];
                 $server->save();
            }

            $this->ptero->sendPowerAction($server->identifier, $request->signal);
            return response()->json(['message' => 'Power signal sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, Server $server)
    {
        if ($request->user()->id !== $server->user_id) {
            abort(403);
        }
        if ($server->status !== 'active') {
            return response()->json(['error' => 'Server is not active'], 422);
        }
        $user = $request->user();
        $product = $server->product;
        $now = now();
        $expires = $server->expires_at ? \Carbon\Carbon::parse($server->expires_at) : $now;
        $totalDays = max(1, $now->diffInDays($now->copy()->addMonth()));
        $remainingDays = max(0, $now->diffInDays($expires, false));
        $fraction = min(1, max(0, $remainingDays / $totalDays));
        $refund = round($product->price_monthly * $fraction, 2);

        try {
            return DB::transaction(function () use ($server, $user, $refund) {
                // Credit balance
                if ($refund > 0) {
                    $user->increment('balance', $refund);
                    $user->payments()->create([
                        'amount' => $refund,
                        'gateway' => 'refund',
                        'status' => 'paid',
                    ]);
                }
                // Delete on panel and mark cancelled
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
        if ($request->user()->id !== $server->user_id) {
            abort(403);
        }
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        $newProduct = \App\Models\Product::findOrFail($request->product_id);
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
            return DB::transaction(function () use ($server, $newProduct, $diff, $request) {
                $user = $request->user();
                // Charge or credit balance for difference
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
                // Update server resources in panel
                $res = $newProduct->resources;
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
                // Update linkage
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
