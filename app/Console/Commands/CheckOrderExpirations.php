<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\GeneralNotification;
use App\Services\PterodactylService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOrderExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend overdue orders and delete orders older than 7 days past expiration';

    /**
     * Execute the console command.
     */
    public function handle(PterodactylService $pterodactylService)
    {
        $now = Carbon::now();
        $this->info("Checking expirations at {$now}...");

        // 0. Auto-Renewal Check (e.g. 1 day before expiration)
        // Find active orders expiring within next 24 hours that have auto-renewal enabled
        $ordersToRenew = Order::where('status', 'active')
            ->where('auto_renewal', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now->copy()->addDay())
            ->where('expires_at', '>', $now) // Not yet expired
            ->get();

        $this->info("Found {$ordersToRenew->count()} orders for potential auto-renewal.");

        foreach ($ordersToRenew as $order) {
            $cost = $order->price;
            $user = $order->user;

            if ($user->balance >= $cost) {
                $this->info("Auto-renewing Order #{$order->id} for user {$user->email}. Cost: {$cost}");

                try {
                    // Deduct balance
                    $user->decrement('balance', $cost);

                    // Log transaction
                    $user->balanceLogs()->create([
                        'amount' => -$cost,
                        'type' => 'renewal',
                        'description' => "Автопродление заказа #{$order->id}",
                    ]);

                    // Extend expiration by 30 days (or service period)
                    $newExpiration = $order->expires_at->copy()->addMonth();
                    $order->update(['expires_at' => $newExpiration]);

                    $this->info("Order #{$order->id} renewed until {$newExpiration}.");

                    // Notify User
                    $user->notify(new GeneralNotification(
                        'Услуга продлена',
                        "Ваш сервер {$order->service->name} успешно продлен до ".$newExpiration->format('d.m.Y').'.',
                        'success',
                        route('orders.show', $order),
                        'Открыть заказ'
                    ));
                } catch (\Exception $e) {
                    $this->error("Failed to auto-renew Order #{$order->id}: ".$e->getMessage());
                }
            } else {
                $this->warn("User {$user->email} has insufficient funds for auto-renewal of Order #{$order->id}. Balance: {$user->balance}, Cost: {$cost}");

                // Notify User about failure
                $user->notify(new GeneralNotification(
                    'Ошибка автопродления',
                    "Не удалось продлить сервер {$order->service->name}. Недостаточно средств на балансе. Сервер будет приостановлен завтра.",
                    'error',
                    route('dashboard.billing'),
                    'Пополнить баланс'
                ));
            }
        }

        // 1. Suspend Active Orders that have expired
        // Explicitly check for 'active' status AND expired date
        $expiredOrders = Order::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        $this->info("Found {$expiredOrders->count()} active orders to suspend.");

        foreach ($expiredOrders as $order) {
            $this->info("Processing Order #{$order->id} (Expired: {$order->expires_at})");

            try {
                // Try to suspend in Pterodactyl first
                if ($order->pterodactyl_server_id) {
                    $this->info("Sending suspend request to Pterodactyl for Server ID: {$order->pterodactyl_server_id}");
                    $pterodactylService->suspendServer($order->pterodactyl_server_id);
                }

                // Update local status regardless (in case Pterodactyl call fails but we want to mark it locally,
                // OR ideally we only update if successful. But if server is deleted in panel manually, we still want to suspend order locally)
                // Let's assume if suspend throws exception (e.g. server not found), we still might want to suspend local order or handle it.
                // For now, let's update status inside try block to ensure we don't update if major error occurs,
                // BUT we should catch "Server Not Found" and still suspend local order.

                $order->update(['status' => 'suspended']);
                $this->info("Order #{$order->id} status updated to 'suspended'.");

                // Notify User
                $order->user->notify(new GeneralNotification(
                    'Услуга приостановлена',
                    "Срок действия сервера {$order->service->name} истек. Работа сервера приостановлена. Пожалуйста, продлите услугу, чтобы избежать удаления.",
                    'warning',
                    route('orders.show', $order),
                    'Продлить сейчас'
                ));

            } catch (\Exception $e) {
                $this->error("Failed to suspend Order #{$order->id}: ".$e->getMessage());

                // If server 404s, it might be already deleted. We should probably mark order suspended/cancelled?
                // For safety, we log error and don't update status so admin can check.
            }
        }

        // 2. Terminate Suspended Orders older than 7 days
        $terminationDate = $now->copy()->subDays(7);
        $ordersToTerminate = Order::where('status', 'suspended')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $terminationDate)
            ->get();

        foreach ($ordersToTerminate as $order) {
            $this->warn("Terminating Order #{$order->id} (Expired > 7 days ago: {$order->expires_at})");

            try {
                if ($order->pterodactyl_server_id) {
                    $pterodactylService->deleteServer($order->pterodactyl_server_id);
                }

                // We keep the order record but mark it as cancelled/terminated and clear server ID
                $order->update([
                    'status' => 'cancelled',
                    'pterodactyl_server_id' => null,
                    'server_ip' => null,
                    'server_port' => null,
                ]);

                // Notify User
                $order->user->notify(new GeneralNotification(
                    'Услуга удалена',
                    "Сервер {$order->service->name} был удален, так как не был оплачен более 7 дней.",
                    'error'
                ));

            } catch (\Exception $e) {
                $this->error("Failed to terminate Order #{$order->id}: ".$e->getMessage());
            }
        }

        // 3. Notify about upcoming expiration (e.g. 3 days before)
        // Find active orders expiring in exactly 3 days
        $ordersExpiringSoon = Order::where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', $now->copy()->addDays(3)->toDateString())
            ->get();

        foreach ($ordersExpiringSoon as $order) {
            $order->user->notify(new GeneralNotification(
                'Истекает срок аренды',
                "Срок аренды сервера {$order->service->name} истекает через 3 дня (".$order->expires_at->format('d.m.Y').').',
                'info',
                route('orders.show', $order),
                'Продлить'
            ));
        }

        $this->info('Expiration check completed.');
    }
}
