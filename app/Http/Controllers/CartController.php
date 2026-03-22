<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureService;
use App\Models\Order;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cartItems = $user->orders()->where('status', 'cart')->with('service')->get();

        return view('dashboard.cart.index', compact('cartItems'));
    }

    public function add(Request $request, InfrastructureService $service)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Проверка на ограничение "Один на пользователя", если применимо
        if ($service->one_per_user) {
            $exists = $user->orders()
                ->where('infrastructure_service_id', $service->id)
                ->whereIn('status', ['active', 'suspended', 'pending', 'paid', 'cart'])
                ->exists();

            if ($exists) {
                return back()->with('error', 'Вы уже используете этот тариф или он уже в корзине.');
            }
        }

        $order = Order::create([
            'user_id' => $user->id,
            'infrastructure_service_id' => $service->id,
            'status' => 'cart',
            'cart_added_at' => now(),
            'price' => $service->price,
        ]);

        $this->auditLogger->log('cart_added', ['service_id' => $service->id], 'order', (string) $order->id);

        return redirect()->route('cart.index')->with('success', 'Услуга добавлена в корзину. Она будет храниться 7 дней.');
    }

    public function remove(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'cart') {
            abort(403);
        }

        $order->delete(); // Soft Delete для истории

        $this->auditLogger->log('cart_removed', [], 'order', (string) $order->id);

        return back()->with('success', 'Заказ удален из корзины.');
    }

    public function checkout(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'cart') {
            abort(403);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->balance < $order->price) {
            $diff = $order->price - $user->balance;
            return redirect()->route('orders.create', $order->service)->with('error', "Недостаточно средств. Нужно пополнить на {$diff} ₽.");
        }

        // Переиспользуем логику из OrderController или вызываем ее
        return redirect()->route('orders.create', $order->service);
    }
}
