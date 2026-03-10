<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureService;
use App\Models\Order;
use App\Services\PterodactylService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $pterodactylService;

    public function __construct(PterodactylService $pterodactylService)
    {
        $this->pterodactylService = $pterodactylService;
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $orders = $user->orders()->with('service')->latest()->paginate(10);

        return view('dashboard.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('dashboard.orders.show', compact('order'));
    }

    public function create(InfrastructureService $service)
    {
        // Simple order confirmation page
        return view('dashboard.orders.create', compact('service'));
    }

    public function store(Request $request, InfrastructureService $service)
    {
        // Check "One Per User" limit
        if ($service->one_per_user) {
            $existingOrder = Order::where('user_id', Auth::id())
                ->where('infrastructure_service_id', $service->id)
                ->whereIn('status', ['active', 'suspended', 'pending', 'paid'])
                ->exists();

            if ($existingOrder) {
                return redirect()->back()->withErrors(['error' => 'Вы уже используете этот тариф. Доступен только один экземпляр на аккаунт.']);
            }
        }

        // Mock payment flow
        $order = Order::create([
            'user_id' => Auth::id(),
            'infrastructure_service_id' => $service->id,
            'status' => 'pending',
            'price' => $service->price,
            'payload' => $request->input('payload', []), // e.g. node selection
        ]);

        // Simulate payment success immediately
        $order->update(['paid_at' => now(), 'status' => 'paid']);

        // Trigger Provisioning
        try {
            // Check if service is "Panel" category (assuming category ID 1 or slug 'panel' or similar)
            // Ideally we check $service->category->slug === 'panel' or similar
            // For now, assume if it has 'egg_id' in specs, it's Pterodactyl
            if (isset($service->specifications['egg_id'])) {
                $this->pterodactylService->provisionServer($order);
            }

            return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно создан и сервер установлен!');
        } catch (Exception $e) {
            return redirect()->route('orders.show', $order)->with('error', 'Заказ создан, но возникла ошибка при установке сервера: '.$e->getMessage());
        }
    }

    public function toggleAutoRenewal(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->update(['auto_renewal' => ! $order->auto_renewal]);

        return back()->with('success', 'Настройка автопродления обновлена.');
    }

    public function renew(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'suspended' && $order->status !== 'active') {
            return back()->with('error', 'Этот заказ нельзя продлить вручную.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cost = $order->price;

        if ($user->balance < $cost) {
            return back()->with('error', 'Недостаточно средств на балансе.');
        }

        try {
            $user->decrement('balance', $cost);

            // Log transaction
            $user->balanceLogs()->create([
                'amount' => -$cost,
                'type' => 'renewal',
                'description' => "Ручное продление заказа #{$order->id}",
            ]);

            // Extend expiration
            $newExpiration = $order->expires_at && $order->expires_at->isFuture()
                ? $order->expires_at->copy()->addMonth()
                : \Carbon\Carbon::now()->addMonth();

            $order->update(['expires_at' => $newExpiration]);

            // Unsuspend if needed
            if ($order->status === 'suspended') {
                if ($order->pterodactyl_server_id) {
                    $this->pterodactylService->unsuspendServer($order->pterodactyl_server_id);
                }
                $order->update(['status' => 'active']);
            }

            return back()->with('success', 'Услуга успешно продлена до '.$newExpiration->format('d.m.Y'));
        } catch (Exception $e) {
            return back()->with('error', 'Ошибка продления: '.$e->getMessage());
        }
    }
}
