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
            ->where(function ($query) {
                $query->where('sync_attempts', '<', 3)
                    ->orWhereNull('sync_attempts');
            })
            ->orderBy('last_sync_at', 'asc')
            ->limit($limit)
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('No pending payments to sync.');

            return;
        }

        $this->info("Syncing {$pendingPayments->count()} payments...");

        foreach ($pendingPayments as $payment) {
            // Check exponential backoff (5, 15, 45 seconds)
            $backoffSeconds = [5, 15, 45];
            $attempts = (int) $payment->sync_attempts;
            
            if ($payment->last_sync_at && $attempts > 0) {
                $wait = $backoffSeconds[min($attempts - 1, 2)];
                if ($payment->last_sync_at->addSeconds($wait)->isFuture()) {
                    $this->line("Skipping #{$payment->id}: too early for attempt #".($attempts + 1));
                    continue;
                }
            }

            try {
                $state = $apiService->getPaymentState($payment->payment_id);
                $status = $state['Status'] ?? null;

                if ($status) {
                    $this->line("Payment #{$payment->id} (ID: {$payment->payment_id}): Current status is {$status}");
                    
                    $apiController->syncStatus($payment, $status, $state);

                    // Update sync info
                    $payment->update([
                        'sync_attempts' => 0, // Reset on success
                        'last_sync_at' => now(),
                        'error_message' => null,
                    ]);
                } else {
                    throw new \Exception('Empty status in T-Bank response');
                }
            } catch (\Exception $e) {
                $newAttempts = $attempts + 1;
                $this->error("Error syncing payment #{$payment->id}: ".$e->getMessage());
                
                $updateData = [
                    'sync_attempts' => $newAttempts,
                    'last_sync_at' => now(),
                    'error_message' => $e->getMessage(),
                ];

                if ($newAttempts >= 3) {
                    $updateData['status'] = 'failed';
                    $this->warn("Payment #{$payment->id} marked as FAILED after {$newAttempts} attempts.");
                }

                $payment->update($updateData);
                
                Log::error('SyncTBankPayments Error', [
                    'payment_id' => $payment->id, 
                    'error' => $e->getMessage(),
                    'attempt' => $newAttempts
                ]);
            }
        }

        $this->info('Sync completed.');
    }
}
