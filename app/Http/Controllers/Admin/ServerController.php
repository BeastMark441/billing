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

        $server = $provisioning->provision($order);

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
}
