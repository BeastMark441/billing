<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Receipt;
use App\Models\TBankWebhookEvent;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use App\Services\ReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessTBankWebhookEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 8;

    public function backoff(): array
    {
        return [5, 15, 30, 60, 120, 300, 600, 900];
    }

    public function __construct(public int $eventId) {}

    public function handle(AuditLogger $auditLogger, ReceiptService $receiptService): void
    {
        /** @var TBankWebhookEvent|null $event */
        $event = TBankWebhookEvent::find($this->eventId);
        if (! $event) {
            return;
        }

        if ($event->processed_at) {
            return;
        }

        if (! $event->signature_valid) {
            $event->update([
                'processed_at' => now(),
                'process_result' => 'rejected',
                'error_message' => 'Invalid signature',
            ]);

            return;
        }

        $orderId = $event->order_id;
        $paymentId = $orderId ? explode('_', $orderId)[0] : null;
        if (! $paymentId) {
            $event->update([
                'processed_at' => now(),
                'process_result' => 'error',
                'error_message' => 'Missing OrderId',
            ]);

            return;
        }

        $receiptUser = null;
        $receiptAmount = null;
        $receiptContext = [];

        DB::transaction(function () use ($auditLogger, $event, $paymentId, &$receiptUser, &$receiptAmount, &$receiptContext) {
            /** @var Payment|null $payment */
            $payment = Payment::lockForUpdate()->find($paymentId);
            if (! $payment) {
                $event->update([
                    'processed_at' => now(),
                    'process_result' => 'error',
                    'error_message' => 'Payment not found',
                ]);

                return;
            }

            $status = strtoupper((string) $event->status);
            $payment->update([
                'status' => strtolower($status),
                'payment_id' => $event->provider_payment_id ?: $payment->payment_id,
                'payload' => array_merge($payment->payload ?? [], ['webhook' => $event->payload]),
            ]);

            if ($status === 'CONFIRMED') {
                if ($payment->credited_at) {
                    $event->update([
                        'processed_at' => now(),
                        'process_result' => 'ok',
                    ]);

                    return;
                }

                $user = $payment->user()->lockForUpdate()->first();
                if (! $user) {
                    throw new \Exception('User not found for payment '.$payment->id);
                }

                $user->increment('balance', $payment->amount);
                $user->balanceLogs()->create([
                    'amount' => $payment->amount,
                    'type' => 'deposit',
                    'description' => 'Пополнение баланса (T-Bank #'.$payment->id.')',
                ]);

                $payment->update(['credited_at' => now()]);

                $user->notify(new GeneralNotification(
                    'Баланс пополнен',
                    'Ваш баланс успешно пополнен на '.number_format((float) $payment->amount, 2, '.', ' ').' ₽.',
                    'success',
                    route('dashboard.billing'),
                    'Перейти к финансам'
                ));

                $auditLogger->log(
                    'payment_confirmed',
                    ['payment_id' => $payment->id, 'provider_payment_id' => $payment->payment_id],
                    'payment',
                    (string) $payment->id,
                    'info'
                );

                $receiptUser = $user;
                $receiptAmount = (float) $payment->amount;
                $receiptContext = [
                    'payment_method' => 'T-Bank',
                    'related_type' => 'payment',
                    'related_id' => (string) $payment->id,
                    'provider_payment_id' => (string) ($payment->payment_id ?? ''),
                ];
            }

            $event->update([
                'processed_at' => now(),
                'process_result' => 'ok',
            ]);
        });

        if ($receiptUser && $receiptAmount !== null) {
            $existing = Receipt::query()
                ->where('type', 'deposit')
                ->where('related_type', 'payment')
                ->where('related_id', (string) ($receiptContext['related_id'] ?? ''))
                ->first();

            if (! $existing) {
                try {
                    $receiptService->issueForDeposit($receiptUser, (float) $receiptAmount, $receiptContext);
                } catch (\Throwable $e) {
                    $auditLogger->log('receipt_issue_failed', ['error' => $e->getMessage(), 'context' => $receiptContext], 'payment', (string) ($receiptContext['related_id'] ?? ''), 'error');
                }
            }
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Failed processing TBank webhook event', ['event_id' => $this->eventId, 'error' => $e->getMessage()]);
        TBankWebhookEvent::whereKey($this->eventId)->update([
            'process_result' => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }
}
