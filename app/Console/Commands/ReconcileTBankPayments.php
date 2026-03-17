<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTBankWebhookEvent;
use App\Models\Payment;
use App\Models\TBankWebhookEvent;
use App\Services\TBankService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReconcileTBankPayments extends Command
{
    protected $signature = 'payments:reconcile-tbank {--limit=50} {--minutes=5}';

    protected $description = 'Проверяет pending платежи T-Bank через GetState и зачисляет баланс при CONFIRMED.';

    public function handle(TBankService $tbankService): int
    {
        $limit = (int) $this->option('limit');
        $minutes = (int) $this->option('minutes');

        $query = Payment::query()
            ->whereNull('credited_at')
            ->whereNotNull('payment_id')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(max(0, $minutes)))
            ->latest();

        $payments = $query->limit(max(1, $limit))->get();

        $processed = 0;
        $credited = 0;

        foreach ($payments as $payment) {
            $processed++;
            $this->line('Checking payment #'.$payment->id.' (PaymentId='.$payment->payment_id.')');

            try {
                $state = $tbankService->getState($payment->payment_id);
                $status = strtoupper((string) ($state['Status'] ?? ''));

                $payload = [
                    'source' => 'cli:getState',
                    'state' => $state,
                ];

                $eventHash = hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $event = TBankWebhookEvent::firstOrCreate(
                    ['event_hash' => $eventHash],
                    [
                        'order_id' => (string) ($payment->id.'_reconcile_'.Str::random(6)),
                        'provider_payment_id' => (string) $payment->payment_id,
                        'status' => $status,
                        'signature_valid' => true,
                        'payload' => $payload,
                    ]
                );

                if (! $event->processed_at) {
                    ProcessTBankWebhookEvent::dispatchSync($event->id);
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
