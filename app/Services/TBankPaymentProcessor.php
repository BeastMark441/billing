<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Receipt;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;

class TBankPaymentProcessor
{
    public function __construct(
        protected AuditLogger $auditLogger,
        protected ReceiptService $receiptService,
    ) {}

    public function applyProviderStatus(
        Payment $payment,
        string $providerStatus,
        ?string $providerPaymentId,
        array $providerPayload,
        string $source,
    ): void {
        $status = strtoupper(trim($providerStatus));

        DB::transaction(function () use ($payment, $status, $providerPaymentId, $providerPayload, $source) {
            /** @var Payment|null $lockedPayment */
            $lockedPayment = Payment::lockForUpdate()->find($payment->id);
            if (! $lockedPayment) {
                return;
            }

            $lockedPayment->update([
                'status' => strtolower($status ?: $lockedPayment->status),
                'payment_id' => $providerPaymentId ?: $lockedPayment->payment_id,
                'payload' => array_merge($lockedPayment->payload ?? [], [
                    $source => $providerPayload,
                ]),
            ]);

            if ($status !== 'CONFIRMED') {
                return;
            }

            if ($lockedPayment->credited_at) {
                return;
            }

            $user = $lockedPayment->user()->lockForUpdate()->first();
            if (! $user) {
                throw new \Exception('User not found for payment '.$lockedPayment->id);
            }

            $user->increment('balance', $lockedPayment->amount);
            $user->balanceLogs()->create([
                'amount' => $lockedPayment->amount,
                'type' => 'deposit',
                'description' => 'Пополнение баланса (T-Bank #'.$lockedPayment->id.')',
            ]);

            $lockedPayment->update(['credited_at' => now()]);

            $user->notify(new GeneralNotification(
                'Баланс пополнен',
                'Ваш баланс успешно пополнен на '.number_format((float) $lockedPayment->amount, 2, '.', ' ').' ₽.',
                'success',
                route('dashboard.billing'),
                'Перейти к финансам'
            ));

            $this->auditLogger->log(
                'payment_confirmed',
                ['payment_id' => $lockedPayment->id, 'provider_payment_id' => $lockedPayment->payment_id, 'source' => $source],
                'payment',
                (string) $lockedPayment->id,
                'info'
            );
        });

        $fresh = $payment->fresh();
        if (! $fresh || ! $fresh->credited_at) {
            return;
        }

        $existingReceipt = Receipt::query()
            ->where('type', 'deposit')
            ->where('related_type', 'payment')
            ->where('related_id', (string) $fresh->id)
            ->first();

        if ($existingReceipt) {
            return;
        }

        try {
            $this->receiptService->issueForDeposit($fresh->user, (float) $fresh->amount, [
                'payment_method' => 'T-Bank',
                'related_type' => 'payment',
                'related_id' => (string) $fresh->id,
                'provider_payment_id' => (string) ($fresh->payment_id ?? ''),
            ]);
        } catch (\Throwable $e) {
            $this->auditLogger->log('receipt_issue_failed', ['error' => $e->getMessage(), 'payment_id' => $fresh->id], 'payment', (string) $fresh->id, 'error');
        }
    }
}
