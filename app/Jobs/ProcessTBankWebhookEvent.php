<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\TBankWebhookEvent;
use App\Services\AuditLogger;
use App\Services\TBankPaymentProcessor;
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

    public function handle(AuditLogger $auditLogger, TBankPaymentProcessor $processor): void
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

        $payload = null;
        $providerStatus = null;
        $providerPaymentId = null;

        DB::transaction(function () use ($event, $paymentId, &$payload, &$providerStatus, &$providerPaymentId) {
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
            $providerStatus = $status;
            $providerPaymentId = $event->provider_payment_id ?: $payment->payment_id;
            $payload = $event->payload;
            $payment->update([
                'status' => strtolower($status),
                'payment_id' => $event->provider_payment_id ?: $payment->payment_id,
                'payload' => array_merge($payment->payload ?? [], ['webhook' => $event->payload]),
            ]);

            $event->update([
                'processed_at' => now(),
                'process_result' => 'ok',
            ]);
        });

        $payment = Payment::find($paymentId);
        if ($payment && $providerStatus && $payload) {
            $processor->applyProviderStatus(
                payment: $payment,
                providerStatus: $providerStatus,
                providerPaymentId: $providerPaymentId,
                providerPayload: (array) $payload,
                source: 'webhook',
            );
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
