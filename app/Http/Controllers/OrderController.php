<?php

namespace App\Http\Controllers;

use App\Jobs\ProvisionPterodactylServer;
use App\Models\InfrastructureService;
use App\Models\Order;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use App\Services\PterodactylService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $pterodactylService;

    public function __construct(PterodactylService $pterodactylService, protected AuditLogger $auditLogger)
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

        $this->auditLogger->log('order_created', ['service_id' => $service->id, 'price' => (float) $service->price], 'order', (string) $order->id);

        // Simulate payment success immediately
        $order->update(['paid_at' => now(), 'status' => 'paid']);

        $order->user->notify(new GeneralNotification(
            'Заказ создан',
            'Заказ #'.$order->id.' создан и оплачен. Начинаем подготовку услуги.',
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

        // Trigger Provisioning
        if (isset($service->specifications['egg_id'])) {
            $order->update(['status' => 'provisioning']);
            ProvisionPterodactylServer::dispatch($order->id);

            return redirect()->route('orders.show', $order)->with('success', 'Заказ создан. Сервер разворачивается, это может занять несколько минут.');
        }

        return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно создан.');
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
