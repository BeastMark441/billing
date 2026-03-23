<?php

namespace App\Console\Commands;

use App\Http\Controllers\TBankApiController;
use App\Models\Payment;
use App\Services\TBankApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncTBankPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:sync-tbank {--limit=50 : Number of payments to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize pending T-Bank payments status via API';

    /**
     * Execute the console command.
     */
    public function handle(TBankApiService $apiService, TBankApiController $apiController)
    {
        $limit = (int) $this->option('limit');

        $pendingPayments = Payment::whereNull('credited_at')
            ->whereNotNull('payment_id')
            ->whereIn('status', ['pending', 'authorized', 'new', 'started'])
            ->orderBy('updated_at', 'asc')
            ->limit($limit)
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('No pending payments to sync.');

            return;
        }

        $this->info("Syncing {$pendingPayments->count()} payments...");

        foreach ($pendingPayments as $payment) {
            try {
                $state = $apiService->getPaymentState($payment->payment_id);
                $status = $state['Status'] ?? null;

                if ($status) {
                    $this->line("Payment #{$payment->id} (ID: {$payment->payment_id}): Current status is {$status}");

                    // We use a reflection or helper to call protected processStatusUpdate or just wrap the logic
                    // For simplicity, let's use the public webhook-like processing logic
                    // But here we need to call the internal processing logic from the controller
                    // Actually, it's better to move that logic to the service or a dedicated processor class.
                    // Given the current structure, I'll call a dedicated method I'll add to the controller or use it directly.

                    // Accessing protected method via reflection for now, or I should have made it public.
                    // Let's assume we can call it if we make it public in TBankApiController.
                    $apiController->syncStatus($payment, $status, $state);
                }
            } catch (\Exception $e) {
                $this->error("Error syncing payment #{$payment->id}: ".$e->getMessage());
                Log::error('SyncTBankPayments Error', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info('Sync completed.');
    }
}
