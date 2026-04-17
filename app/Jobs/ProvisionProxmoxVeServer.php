<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use App\Services\ProxmoxVeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProvisionProxmoxVeServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function backoff(): array
    {
        return [10, 30, 60, 120, 300];
    }

    public function __construct(public int $orderId) {}

    public function handle(ProxmoxVeService $proxmoxVeService, AuditLogger $auditLogger): void
    {
        /** @var Order|null $order */
        $order = Order::with(['user', 'service'])->find($this->orderId);
        if (! $order) {
            return;
        }

        try {
            $proxmoxVeService->provisionServer($order);

            $order->user->notify(new GeneralNotification(
                'Сервер создан',
                'Виртуальный сервер для заказа #'.$order->id.' успешно создан.',
                'success',
                route('orders.show', $order),
                'Открыть заказ'
            ));

            $auditLogger->log('proxmox_provision_success', ['order_id' => $order->id], 'order', (string) $order->id);
        } catch (\Throwable $e) {
            $order->update([
                'status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);

            $order->user->notify(new GeneralNotification(
                'Ошибка создания сервера',
                'Заказ #'.$order->id.' создан, но виртуальный сервер не удалось развернуть. Поддержка уже уведомлена.',
                'error',
                route('orders.show', $order),
                'Открыть заказ'
            ));

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'Ошибка ProxmoxVE',
                    'Ошибка при создании виртуального сервера для заказа #'.$order->id.': '.$e->getMessage(),
                    'error',
                    route('admin.orders.show', $order),
                    'Открыть заказ'
                ));
            }

            $auditLogger->log('proxmox_provision_failed', ['order_id' => $order->id, 'error' => $e->getMessage()], 'order', (string) $order->id, 'error');

            throw $e;
        }
    }
}

