<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupAbandonedCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove orders from cart after 7 days of inactivity';

    /**
     * Execute the console command.
     */
    public function handle(AuditLogger $auditLogger)
    {
        $expiryDate = Carbon::now()->subDays(7);

        $abandonedOrders = Order::where('status', 'cart')
            ->where(function ($query) use ($expiryDate) {
                $query->where('cart_added_at', '<=', $expiryDate)
                    ->orWhere(function ($q) use ($expiryDate) {
                        $q->whereNull('cart_added_at')
                            ->where('created_at', '<=', $expiryDate);
                    });
            })
            ->get();

        if ($abandonedOrders->isEmpty()) {
            $this->info('No abandoned cart items to cleanup.');

            return;
        }

        $this->info("Cleaning up {$abandonedOrders->count()} cart items...");

        foreach ($abandonedOrders as $order) {
            $user = $order->user;
            $serviceName = $order->service->name;

            // Notify user
            try {
                $user->notify(new GeneralNotification(
                    'Корзина очищена',
                    "Услуга '{$serviceName}' была удалена из вашей корзины, так как срок ее хранения (7 дней) истек.",
                    'info',
                    route('dashboard.infrastructure'),
                    'Вернуться в каталог'
                ));
            } catch (\Exception $e) {
                $this->error("Failed to notify user {$user->email}: ".$e->getMessage());
            }

            // Log audit
            $auditLogger->log('cart_auto_cleanup', ['service_name' => $serviceName], 'order', (string) $order->id);

            // Soft delete
            $order->delete();

            $this->line("Removed Order #{$order->id} from {$user->email} cart.");
        }

        $this->info('Cleanup completed.');
    }
}
