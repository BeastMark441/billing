<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PterodactylService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $pterodactylService;

    public function __construct(PterodactylService $pterodactylService)
    {
        $this->pterodactylService = $pterodactylService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'service']);

        // Filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('pterodactyl_server_identifier', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,suspended,cancelled,pending,paid,failed',
            'price' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        $order->update($validated);

        // Handle Pterodactyl actions based on status change
        if ($oldStatus !== $newStatus && $order->pterodactyl_server_id) {
            try {
                if ($newStatus === 'suspended' && $oldStatus === 'active') {
                    $this->pterodactylService->suspendServer($order->pterodactyl_server_id);
                } elseif ($newStatus === 'active' && $oldStatus === 'suspended') {
                    $this->pterodactylService->unsuspendServer($order->pterodactyl_server_id);
                }
            } catch (\Exception $e) {
                return redirect()->route('admin.orders.show', $order)
                    ->with('error', 'Статус обновлен в базе, но ошибка при синхронизации с панелью: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Заказ успешно обновлен.');
    }

    public function destroy(Order $order)
    {
        // Optional: Delete server from Pterodactyl
        if ($order->pterodactyl_server_id) {
            try {
                $this->pterodactylService->deleteServer($order->pterodactyl_server_id);
            } catch (\Exception $e) {
                // Log error but continue with DB deletion or return error
                // For now, let's continue but warn
            }
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Заказ удален.');
    }
}
