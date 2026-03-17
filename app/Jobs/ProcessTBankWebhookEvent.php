<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\TBankWebhookEvent;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
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

    public function handle(AuditLogger $auditLogger): void
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

        DB::transaction(function () use ($auditLogger, $event, $paymentId) {
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

                $payment->user()->lockForUpdate()->first();
                $payment->user->increment('balance', $payment->amount);
                $payment->user->balanceLogs()->create([
                    'amount' => $payment->amount,
                    'type' => 'deposit',
                    'description' => 'Пополнение баланса (T-Bank #'.$payment->id.')',
                ]);

                $payment->update(['credited_at' => now()]);

                $payment->user->notify(new GeneralNotification(
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
            }

            $event->update([
                'processed_at' => now(),
                'process_result' => 'ok',
            ]);
        });
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
