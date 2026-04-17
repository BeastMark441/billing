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

class OrderController extends Controller
{
    protected $pterodactylService;

    public function __construct(PterodactylService $pterodactylService, protected AuditLogger $auditLogger, protected ReceiptService $receiptService)
    {
        $this->pterodactylService = $pterodactylService;
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $orders = $user->orders()
            ->whereIn('status', ['paid', 'active', 'pending', 'awaiting', 'suspended', 'provisioning', 'failed', 'cancelled', 'expired'])
            ->with('service')
            ->latest()
            ->paginate(10);

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
                ->whereIn('status', ['active', 'suspended', 'pending', 'paid', 'awaiting', 'provisioning'])
                ->exists();

            if ($existingOrder) {
                return redirect()->back()->withErrors(['error' => 'Вы уже используете этот тариф. Доступен только один экземпляр на аккаунт.']);
            }
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $payload = $request->input('payload', []);
        $price = (float) $service->price;

        try {
            $order = DB::transaction(function () use ($payload, $price, $service, $user) {
                $lockedUser = User::whereKey($user->id)->lockForUpdate()->first();
                if (! $lockedUser) {
                    throw new Exception('Пользователь не найден.');
                }

                if ((float) $lockedUser->balance < $price) {
                    $diff = $price - (float) $lockedUser->balance;
                    throw new Exception("Недостаточно средств на балансе. Необходимо пополнить еще на {$diff} ₽.");
                }

                $affected = User::whereKey($lockedUser->id)->decrement('balance', $price);
                if ($affected !== 1) {
                    throw new Exception('Не удалось списать средства с баланса.');
                }

                $lockedUser->balanceLogs()->create([
                    'amount' => -$price,
                    'type' => 'purchase',
                    'description' => 'Покупка услуги: '.$service->name,
                ]);

                $integrationType = $service->integration_type ?: (isset(($service->specifications ?? [])['egg_id']) ? 'pterodactyl' : 'legacy');
                $startsBillingImmediately = ! in_array($integrationType, ['service', 'other'], true);
                $expiresAt = $startsBillingImmediately ? Carbon::now()->addMonth() : null;
                $initialStatus = $startsBillingImmediately ? 'paid' : 'awaiting';

                $order = Order::create([
                    'user_id' => $lockedUser->id,
                    'infrastructure_service_id' => $service->id,
                    'status' => $initialStatus,
                    'price' => $service->price,
                    'payload' => $payload,
                    'paid_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                $this->auditLogger->log('order_created', ['service_id' => $service->id, 'price' => $price], 'order', (string) $order->id);

                return $order;
            });

            $orderMessage = $order->status === 'awaiting'
                ? 'Заказ #'.$order->id.' создан и оплачен. Статус: ожидание исполнителя.'
                : 'Заказ #'.$order->id.' создан и оплачен. Начинаем подготовку услуги.';

            $order->user->notify(new GeneralNotification(
                'Заказ создан',
                $orderMessage,
                'success',
                route('orders.show', $order),
                'Открыть заказ'
            ));

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'Новый заказ',
                    'Создан новый заказ #'.$order->id.' ('.$service->name.').',
                    'info',
                    route('admin.orders.show', $order),
                    'Открыть заказ'
                ));
            }

            $existingReceipt = \App\Models\Receipt::query()
                ->where('type', 'purchase')
                ->where('related_type', 'order')
                ->where('related_id', (string) $order->id)
                ->first();

            if (! $existingReceipt) {
                try {
                    $this->receiptService->issueForPurchase($order->user, (string) ($service->name ?? 'Услуга'), (float) $order->price, [
                        'payment_method' => 'Баланс',
                        'related_type' => 'order',
                        'related_id' => (string) $order->id,
                    ]);
                } catch (\Throwable $e) {
                    $this->auditLogger->log('receipt_issue_failed', ['error' => $e->getMessage(), 'order_id' => $order->id], 'order', (string) $order->id, 'error');
                }
            }

            if ($order->status === 'awaiting') {
                return redirect()->route('orders.show', $order)->with('success', 'Заказ создан. Ожидайте исполнения, списание/продление начнется после выдачи услуги.');
            }

            $integrationType = $service->integration_type ?: (isset(($service->specifications ?? [])['egg_id']) ? 'pterodactyl' : 'legacy');

            if ($integrationType === 'pterodactyl' || isset(($service->specifications ?? [])['egg_id'])) {
                $order->update(['status' => 'provisioning']);
                ProvisionPterodactylServer::dispatch($order->id);

                return redirect()->route('orders.show', $order)->with('success', 'Заказ создан. Сервер разворачивается, это может занять несколько минут.');
            }

            if ($integrationType === 'proxmoxve') {
                $order->update(['status' => 'provisioning']);
                ProvisionProxmoxVeServer::dispatch($order->id);

                return redirect()->route('orders.show', $order)->with('success', 'Заказ создан. Виртуальный сервер разворачивается, это может занять несколько минут.');
            }

            $order->update(['status' => 'active']);

            return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно создан.');
        } catch (Exception $e) {
            $this->auditLogger->log('order_purchase_failed', ['error' => $e->getMessage(), 'service_id' => $service->id], 'service', (string) $service->id, 'error');

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function toggleAutoRenewal(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->update(['auto_renewal' => ! $order->auto_renewal]);

        $this->auditLogger->log('order_auto_renewal_toggled', ['enabled' => (bool) $order->auto_renewal], 'order', (string) $order->id);

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

            $user->notify(new GeneralNotification(
                'Услуга продлена',
                'Заказ #'.$order->id.' продлен до '.$newExpiration->format('d.m.Y').'.',
                'success',
                route('orders.show', $order),
                'Открыть заказ'
            ));

            $this->auditLogger->log('order_renewed', ['amount' => (float) $cost, 'expires_at' => $newExpiration->toAtomString()], 'order', (string) $order->id);

            return back()->with('success', 'Услуга успешно продлена до '.$newExpiration->format('d.m.Y'));
        } catch (Exception $e) {
            $this->auditLogger->log('order_renew_failed', ['error' => $e->getMessage()], 'order', (string) $order->id, 'error');

            return back()->with('error', 'Ошибка продления: '.$e->getMessage());
        }
    }
}
