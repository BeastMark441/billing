<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\PterodactylService;
use Illuminate\Console\Command;

class SyncServerStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:sync-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local order status with Pterodactyl server status';

    /**
     * Execute the console command.
     */
    public function handle(PterodactylService $pterodactylService)
    {
        $orders = Order::whereNotNull('pterodactyl_server_id')
            ->whereIn('status', ['active', 'suspended'])
            ->get();

        $this->info("Checking status for {$orders->count()} orders...");

        foreach ($orders as $order) {
            try {
                $server = $pterodactylService->getServerDetails($order->pterodactyl_server_id);

                if (! $server) {
                    $this->warn("Server for Order #{$order->id} not found in Pterodactyl (404).");

                    // Optionally mark as cancelled or failed?
                    continue;
                }

                $isSuspended = $server['suspended'] ?? false;
                $localStatus = $order->status;

                if ($isSuspended && $localStatus === 'active') {
                    $this->info("Order #{$order->id}: Remote is SUSPENDED, Local is ACTIVE. Updating to SUSPENDED.");
                    $order->update(['status' => 'suspended']);
                } elseif (! $isSuspended && $localStatus === 'suspended') {
                    // Be careful here: maybe it was suspended by admin locally?
                    // But if it's active in panel, it should probably be active locally unless unpaid.
                    // Let's check expiration.
                    if ($order->expires_at && $order->expires_at->isPast()) {
                        $this->warn("Order #{$order->id}: Remote is ACTIVE, Local is SUSPENDED (Expired). Suspending remote...");
                        $pterodactylService->suspendServer($order->pterodactyl_server_id);
                    } else {
                        $this->info("Order #{$order->id}: Remote is ACTIVE, Local is SUSPENDED (Not Expired). Updating to ACTIVE.");
                        $order->update(['status' => 'active']);
                    }
                }

            } catch (\Exception $e) {
                $this->error("Error checking Order #{$order->id}: ".$e->getMessage());
            }
        }

        $this->info('Sync completed.');
    }
}
