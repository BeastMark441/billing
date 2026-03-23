<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use App\Services\ReceiptService;
use App\Services\TBankApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TBankApiController extends Controller
{
    protected TBankApiService $tbankApiService;

    protected AuditLogger $auditLogger;

    protected ReceiptService $receiptService;

    public function __construct(TBankApiService $tbankApiService, AuditLogger $auditLogger, ReceiptService $receiptService)
    {
        $this->tbankApiService = $tbankApiService;
        $this->auditLogger = $auditLogger;
        $this->receiptService = $receiptService;
    }

    /**
     * Create a new payment (top-up)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10|max:1000000',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Create local Payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'status' => 'pending',
            'description' => 'Пополнение баланса личного кабинета',
        ]);

        $this->auditLogger->log('payment_create', ['amount' => $validated['amount']], 'payment', (string) $payment->id);

        try {
            $url = $this->tbankApiService->createPaymentLink($payment);

            return redirect($url);
        } catch (\Exception $e) {
            $payment->update([
                'status' => 'error',
                'payload' => array_merge($payment->payload ?? [], ['error' => $e->getMessage()]),
            ]);

            $this->auditLogger->log('payment_create_failed', ['error' => $e->getMessage()], 'payment', (string) $payment->id, 'error');

            return back()->with('error', 'Ошибка создания платежа: '.$e->getMessage());
        }
    }

    /**
     * Redirect after success (from T-Bank)
     */
    public function success(Request $request)
    {
        $paymentId = $request->input('PaymentId');
        if (! $paymentId) {
            return redirect()->route('dashboard.billing')->with('success', 'Платеж в процессе обработки.');
        }

        $payment = Payment::where('payment_id', (string) $paymentId)->first();

        // Even if not credited yet, we show success because bank redirected here
        if ($payment && $payment->credited_at) {
            return redirect()->route('dashboard.billing')->with('success', 'Баланс успешно пополнен.');
        }

        return redirect()->route('dashboard.billing')->with('success', 'Платеж принят. Баланс будет пополнен автоматически в течение нескольких минут.');
    }

    /**
     * Redirect after failure (from T-Bank)
     */
    public function failed(Request $request)
    {
        return redirect()->route('dashboard.billing')->with('error', 'Оплата была отменена или произошла ошибка.');
    }

    /**
     * Webhook from T-Bank Acquiring
     */
    public function webhook(Request $request)
    {
        $data = $request->all();

        // 1. Token verification
        if (! $this->tbankApiService->verifyWebhook($data)) {
            Log::warning('TBank Webhook: Invalid Token', ['payload' => $data]);

            return response('INVALID_TOKEN', 400);
        }

        $paymentId = $data['PaymentId'] ?? null;
        $status = $data['Status'] ?? null;

        if (! $paymentId || ! $status) {
            return response('INVALID_PAYLOAD', 400);
        }

        // 2. Idempotency check
        if ($this->tbankApiService->isIdempotent($data)) {
            return response('OK', 200);
        }

        $payment = Payment::where('payment_id', (string) $paymentId)->first();
        if (! $payment) {
            Log::error('TBank Webhook: Payment not found', ['payment_id' => $paymentId]);

            return response('NOT_FOUND', 404);
        }

        // 3. Process status
        try {
            $this->processStatusUpdate($payment, $status, $data);

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('TBank Webhook: Processing error', ['error' => $e->getMessage(), 'payment_id' => $payment->id]);

            return response('ERROR', 500);
        }
    }

    /**
     * Public method to sync status from command or other places
     */
    public function syncStatus(Payment $payment, string $status, array $payload): void
    {
        $this->processStatusUpdate($payment, $status, $payload);
    }

    /**
     * Apply status and update balance
     */
    protected function processStatusUpdate(Payment $payment, string $status, array $payload): void
    {
        $normalizedStatus = strtoupper($status);

        DB::transaction(function () use ($payment, $normalizedStatus, $payload) {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::lockForUpdate()->find($payment->id);
            if (! $lockedPayment) {
                return;
            }

            $lockedPayment->update([
                'status' => strtolower($normalizedStatus),
                'payload' => array_merge($lockedPayment->payload ?? [], ['sync' => $payload]),
            ]);

            // Acquiring confirmed statuses: CONFIRMED or AUTHORIZED (depending on settings, usually CONFIRMED)
            if ($normalizedStatus !== 'CONFIRMED') {
                return;
            }

            if ($lockedPayment->credited_at) {
                return;
            }

            /** @var User $user */
            $user = $lockedPayment->user()->lockForUpdate()->first();
            if (! $user) {
                throw new \Exception('User not found for payment '.$lockedPayment->id);
            }

            // Credit balance
            $user->increment('balance', $lockedPayment->amount);
            $user->balanceLogs()->create([
                'amount' => $lockedPayment->amount,
                'type' => 'deposit',
                'description' => 'Пополнение баланса (T-Bank #'.$lockedPayment->id.')',
            ]);

            $lockedPayment->update(['credited_at' => now()]);

            // Notifications
            $user->notify(new GeneralNotification(
                'Баланс пополнен',
                'Ваш баланс успешно пополнен на '.number_format((float) $lockedPayment->amount, 2, '.', ' ').' ₽.',
                'success',
                route('dashboard.billing'),
                'Перейти к финансам'
            ));

            $this->auditLogger->log(
                'payment_confirmed',
                ['payment_id' => $lockedPayment->id, 'provider_payment_id' => $lockedPayment->payment_id, 'source' => 'sync'],
                'payment',
                (string) $lockedPayment->id,
                'info'
            );

            // Issue Receipt
            try {
                $this->receiptService->issueForDeposit($user, (float) $lockedPayment->amount, [
                    'payment_method' => 'T-Bank',
                    'related_type' => 'payment',
                    'related_id' => (string) $lockedPayment->id,
                    'provider_payment_id' => (string) ($lockedPayment->payment_id ?? ''),
                ]);
            } catch (\Throwable $e) {
                $this->auditLogger->log('receipt_issue_failed', ['error' => $e->getMessage(), 'payment_id' => $lockedPayment->id], 'payment', (string) $lockedPayment->id, 'error');
            }
        });
    }
}
