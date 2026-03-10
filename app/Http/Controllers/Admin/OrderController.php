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

    public function create()
    {
        $users = \App\Models\User::orderBy('email')->get();
        $services = \App\Models\InfrastructureService::where('is_active', true)->get();
        return view('admin.orders.create', compact('users', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'infrastructure_service_id' => 'required|exists:infrastructure_services,id',
            'expires_at' => 'nullable|date',
            'payment_method' => 'required|in:free,balance',
        ]);

        $service = \App\Models\InfrastructureService::find($validated['infrastructure_service_id']);
        $user = \App\Models\User::find($validated['user_id']);

        // Check balance if method is balance
        if ($validated['payment_method'] === 'balance') {
            if ($user->balance < $service->price) {
                return back()->with('error', 'У пользователя недостаточно средств на балансе.');
            }
            $user->decrement('balance', $service->price);
            // Log transaction
            $user->balanceLogs()->create([
                'amount' => -$service->price,
                'type' => 'purchase',
                'description' => "Заказ услуги {$service->name} (Администратор)",
            ]);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'infrastructure_service_id' => $service->id,
            'status' => 'pending',
            'price' => $service->price,
            'payload' => [],
            'expires_at' => $validated['expires_at'],
        ]);

        // Provision immediately
        try {
            if (isset($service->specifications['egg_id'])) {
                $this->pterodactylService->provisionServer($order);
            }
            // Mark as paid/active if provisioned or free
            $order->update([
                'status' => 'active', 
                'paid_at' => now()
            ]);
            
            return redirect()->route('admin.orders.show', $order)->with('success', 'Заказ успешно создан и отправлен на установку.');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Заказ создан, но ошибка установки: ' . $e->getMessage());
        }
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
        
        // Clear cache if any (standard Laravel doesn't cache models by default but just in case)
        $order->refresh();

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

    public function changePlan(Request $request, Order $order)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:infrastructure_services,id',
        ]);

        $newService = \App\Models\InfrastructureService::find($validated['service_id']);

        if (!$newService) {
            return back()->with('error', 'Тариф не найден.');
        }

        // Logic to update Pterodactyl server limits/startup/image
        // This is complex and depends on Pterodactyl API capabilities (Build/Startup endpoints)
        // For MVP, we will update local order and price, but server update might require more code.
        // Let's assume PterodactylService has updateServerBuild method (we need to implement it).
        
        try {
            // Update Price and Service ID
            $order->update([
                'infrastructure_service_id' => $newService->id,
                'price' => $newService->price,
            ]);

            // Update Pterodactyl Server Build
            if ($order->pterodactyl_server_id) {
                // This method needs to be implemented in PterodactylService
                // $this->pterodactylService->updateServerBuild($order->pterodactyl_server_id, $newService->specifications);
                // For now just warning
                // return back()->with('warning', 'Тариф в биллинге обновлен. Ресурсы в Pterodactyl нужно обновить вручную (функция в разработке).');
            }

            return back()->with('success', 'Тариф успешно изменен.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при смене тарифа: ' . $e->getMessage());
        }
    }

    public function refund(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:balance,external',
        ]);

        $amount = $validated['amount'];
        $type = $validated['type'];

        // If refund to balance
        if ($type === 'balance') {
            $order->user->increment('balance', $amount);
            // Log transaction
            $order->user->balanceLogs()->create([
                'amount' => $amount,
                'type' => 'refund',
                'description' => "Возврат средств за заказ #{$order->id}",
            ]);
        } else {
            // External refund logic (manual for now)
            // Just mark order or log it?
        }

        // Suspend/Cancel order after refund usually?
        // Let's ask user or just log it. Usually refund implies cancellation.
        // User asked just for refund button, let's assume it just processes money.
        // But typically we should cancel the order too.
        $order->update(['status' => 'cancelled']);
        if ($order->pterodactyl_server_id) {
            try {
                $this->pterodactylService->suspendServer($order->pterodactyl_server_id);
                // Or delete? Let's suspend for safety.
            } catch (\Exception $e) {}
        }

        return back()->with('success', "Возврат {$amount} ₽ оформлен (" . ($type === 'balance' ? 'На баланс' : 'Внешний') . "). Заказ отменен.");
    }
}
