<?php

namespace App\Http\Controllers;

use App\Jobs\ProvisionPterodactylServer;
use App\Jobs\ProvisionProxmoxVeServer;
use App\Models\InfrastructureService;
use App\Models\Order;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use App\Services\PterodactylService;
use App\Services\ReceiptService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected PterodactylService $pterodactylService,
        protected ReceiptService $receiptService
    ) {}

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
                ->whereIn('status', ['active', 'suspended', 'pending', 'paid', 'awaiting', 'provisioning', 'cart'])
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

    public function checkoutAll()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cartItems = $user->orders()->where('status', 'cart')->with('service')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Ваша корзина пуста.');
        }

        $totalPrice = (float) $cartItems->sum('price');

        if ((float) $user->balance < $totalPrice) {
            $diff = $totalPrice - (float) $user->balance;

            return redirect()->route('cart.index')->with('error', "Недостаточно средств на балансе. Необходимо пополнить еще на {$diff} ₽.");
        }

        $processedCount = 0;
        $failedCount = 0;

        try {
            DB::transaction(function () use ($cartItems, $totalPrice, $user, &$processedCount, &$failedCount) {
                $lockedUser = User::whereKey($user->id)->lockForUpdate()->first();
                if (! $lockedUser) {
                    throw new Exception('Пользователь не найден.');
                }

                if ((float) $lockedUser->balance < $totalPrice) {
                    throw new Exception('Недостаточно средств на балансе.');
                }

                // Списываем общую сумму
                User::whereKey($lockedUser->id)->decrement('balance', $totalPrice);

                $lockedUser->balanceLogs()->create([
                    'amount' => -$totalPrice,
                    'type' => 'purchase',
                    'description' => 'Оплата корзины ('.$cartItems->count().' услуг)',
                ]);

                foreach ($cartItems as $order) {
                    $service = $order->service;
                    $integrationType = $service->integration_type ?: (isset(($service->specifications ?? [])['egg_id']) ? 'pterodactyl' : 'legacy');
                    $startsBillingImmediately = ! in_array($integrationType, ['service', 'other'], true);
                    $expiresAt = $startsBillingImmediately ? Carbon::now()->addMonth() : null;
                    $initialStatus = $startsBillingImmediately ? 'paid' : 'awaiting';

                    $order->update([
                        'status' => $initialStatus,
                        'paid_at' => now(),
                        'expires_at' => $expiresAt,
                    ]);

                    $this->auditLogger->log('order_created', ['service_id' => $service->id, 'price' => $order->price], 'order', (string) $order->id);

                    // Выдача чека
                    try {
                        $this->receiptService->issueForPurchase($lockedUser, (string) ($service->name ?? 'Услуга'), (float) $order->price, [
                            'payment_method' => 'Баланс',
                            'related_type' => 'order',
                            'related_id' => (string) $order->id,
                        ]);
                    } catch (\Throwable $e) {
                        $this->auditLogger->log('receipt_issue_failed', ['error' => $e->getMessage(), 'order_id' => $order->id], 'order', (string) $order->id, 'error');
                    }

                    if ($order->status === 'awaiting') {
                        $processedCount++;
                        continue;
                    }

                    if ($integrationType === 'pterodactyl' || isset(($service->specifications ?? [])['egg_id'])) {
                        $order->update(['status' => 'provisioning']);
                        ProvisionPterodactylServer::dispatch($order->id);
                    } elseif ($integrationType === 'proxmoxve') {
                        $order->update(['status' => 'provisioning']);
                        ProvisionProxmoxVeServer::dispatch($order->id);
                    } else {
                        $order->update(['status' => 'active']);
                    }

                    $processedCount++;
                }
            });

            $user->notify(new GeneralNotification(
                'Корзина оплачена',
                "Вы успешно оплатили {$processedCount} услуг. Начинаем подготовку.",
                'success',
                route('orders.index'),
                'Мои заказы'
            ));

            return redirect()->route('orders.index')->with('success', "Корзина успешно оплачена. Оплачено услуг: {$processedCount}.");
        } catch (Exception $e) {
            $this->auditLogger->log('cart_checkout_failed', ['error' => $e->getMessage()], 'user', (string) $user->id, 'error');

            return redirect()->route('cart.index')->with('error', 'Ошибка при оплате корзины: '.$e->getMessage());
        }
    }
}
