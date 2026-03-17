<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\TBankPaymentProcessor;
use App\Services\TBankService;
use Illuminate\Console\Command;

class ReconcileTBankPayments extends Command
{
    protected $signature = 'payments:reconcile-tbank {--limit=50} {--minutes=5}';

    protected $description = 'Проверяет pending платежи T-Bank через GetState и зачисляет баланс при CONFIRMED. --minutes>0 ограничивает платежи старше N минут.';

    public function handle(TBankService $tbankService, TBankPaymentProcessor $processor): int
    {
        $limit = (int) $this->option('limit');
        $minutes = (int) $this->option('minutes');

        $query = Payment::query()
            ->whereNull('credited_at')
            ->whereNotNull('payment_id')
            ->where('status', 'pending')
            ->latest();

        if ($minutes > 0) {
            $query->where('created_at', '<=', now()->subMinutes($minutes));
        }

        $payments = $query->limit(max(1, $limit))->get();

        $processed = 0;
        $credited = 0;

        foreach ($payments as $payment) {
            $processed++;
            $this->line('Checking payment #'.$payment->id.' (PaymentId='.$payment->payment_id.')');

            try {
                $state = $tbankService->getState($payment->payment_id);
                $status = strtoupper((string) ($state['Status'] ?? ''));

                if ($status !== '') {
                    $processor->applyProviderStatus(
                        payment: $payment,
                        providerStatus: $status,
                        providerPaymentId: (string) $payment->payment_id,
                        providerPayload: ['state' => $state],
                        source: 'getState:cli',
                    );
                }

                $payment->refresh();
                if ($payment->credited_at) {
                    $credited++;
                }
            } catch (\Throwable $e) {
                $this->error('Error: '.$e->getMessage());
            }
        }

        $this->info('Done. Checked: '.$processed.', credited: '.$credited);

        return Command::SUCCESS;
    }
}
