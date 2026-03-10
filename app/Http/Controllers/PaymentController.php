<?php

namespace App\Http\Controllers;

use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $tbankService;

    public function __construct(\App\Services\TBankService $tbankService)
    {
        $this->tbankService = $tbankService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10|max:30000',
        ]);

        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        // Create Payment record
        $payment = \App\Models\Payment::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'status' => 'pending',
            'description' => 'Пополнение баланса',
        ]);

        try {
            $url = $this->tbankService->init($payment);

            return redirect($url);
        } catch (\Exception $e) {
            $payment->update(['status' => 'error', 'payload' => ['error' => $e->getMessage()]]);

            return back()->with('error', 'Ошибка создания платежа: '.$e->getMessage());
        }
    }

    public function success(Request $request)
    {
        // User redirected back from T-Bank
        // Usually we just show "Success" message, real status update comes via Webhook
        // But we can check status manually just in case
        return redirect()->route('dashboard.billing.index')->with('success', 'Платеж обрабатывается. Баланс будет пополнен в ближайшее время.');
    }

    public function failed(Request $request)
    {
        return redirect()->route('dashboard.billing.index')->with('error', 'Оплата не была завершена.');
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        \Illuminate\Support\Facades\Log::info('TBank Webhook', $data);

        // Extract OrderId
        $orderId = $data['OrderId'] ?? null;
        if (! $orderId) {
            return response('Invalid OrderId', 400);
        }

        // Validate Token signature
        // T-Bank sends Token in request. We need to verify it.
        // We MUST verify signature before processing money!
        if (! $this->tbankService->validateCallback($data)) {
            \Illuminate\Support\Facades\Log::warning('TBank Webhook: Invalid Token', $data);

            return response('Invalid Token', 400);
        }

        // Parse payment ID (e.g. 15_123456 -> 15)
        $paymentId = explode('_', $orderId)[0];
        $payment = \App\Models\Payment::find($paymentId);

        if (! $payment) {
            return response('Payment not found', 404);
        }

        $status = $data['Status'] ?? 'UNKNOWN';
        $oldStatus = $payment->status;

        // If payment is already confirmed, ignore (idempotency)
        if ($oldStatus === 'confirmed') {
            return response('OK', 200);
        }

        $payment->update([
            'status' => strtolower($status),
            'payment_id' => $data['PaymentId'] ?? $payment->payment_id,
            'payload' => array_merge($payment->payload ?? [], ['webhook' => $data]),
        ]);

        if ($status === 'CONFIRMED') {
            // Top up user balance
            $payment->user->increment('balance', $payment->amount);

            // Log balance transaction
            // Use 'admin_deposit' or create new type 'deposit'
            $payment->user->balanceLogs()->create([
                'amount' => $payment->amount,
                'type' => 'admin_deposit',
                'description' => 'Пополнение баланса (T-Bank #'.$payment->id.')',
            ]);

            // Send Notification
            $payment->user->notify(new GeneralNotification(
                'Баланс пополнен',
                'Ваш баланс успешно пополнен на '.number_format($payment->amount, 2).' ₽.',
                'success',
                route('dashboard.billing.index'),
                'Перейти к финансам'
            ));

            \Illuminate\Support\Facades\Log::info("Payment #{$payment->id} confirmed via webhook. Balance updated.");
        } elseif ($status === 'REJECTED' || $status === 'CANCELED') {
            // Just log/update status (already done above)
            \Illuminate\Support\Facades\Log::info("Payment #{$payment->id} failed/canceled via webhook.");
        }

        return response('OK', 200);
    }
}
