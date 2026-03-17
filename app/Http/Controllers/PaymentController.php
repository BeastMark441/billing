<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTBankWebhookEvent;
use App\Models\Payment;
use App\Models\TBankWebhookEvent;
use App\Services\AuditLogger;
use App\Services\TBankPaymentProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $tbankService;

    public function __construct(\App\Services\TBankService $tbankService, protected AuditLogger $auditLogger, protected TBankPaymentProcessor $processor)
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

        $this->auditLogger->log('payment_create', ['amount' => $validated['amount']], 'payment', (string) $payment->id);

        try {
            $url = $this->tbankService->init($payment);

            return redirect($url);
        } catch (\Exception $e) {
            $payment->update(['status' => 'error', 'payload' => ['error' => $e->getMessage()]]);

            $this->auditLogger->log('payment_create_failed', ['error' => $e->getMessage()], 'payment', (string) $payment->id, 'error');

            return back()->with('error', 'Ошибка создания платежа: '.$e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $payment = null;

        if ($request->filled('PaymentId')) {
            $payment = Payment::query()->where('payment_id', (string) $request->input('PaymentId'))->first();
        }

        if (! $payment && $request->filled('OrderId')) {
            $parts = explode('_', (string) $request->input('OrderId'));
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $payment = Payment::find((int) $parts[0]);
            }
        }

        if ($payment && $payment->payment_id && ! $payment->credited_at) {
            try {
                $state = $this->tbankService->getState($payment->payment_id);
                $status = strtoupper((string) ($state['Status'] ?? ''));

                $this->auditLogger->log('payment_get_state', ['payment_id' => $payment->id, 'state' => $state], 'payment', (string) $payment->id);

                if ($status !== '') {
                    $this->processor->applyProviderStatus(
                        payment: $payment,
                        providerStatus: $status,
                        providerPaymentId: (string) $payment->payment_id,
                        providerPayload: ['state' => $state],
                        source: 'getState:success',
                    );
                }

                $payment->refresh();
                if ($payment->credited_at) {
                    return redirect()->route('dashboard.billing')->with('success', 'Платёж подтверждён. Баланс пополнен.');
                }
            } catch (\Throwable $e) {
                $this->auditLogger->log('payment_get_state_failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()], 'payment', (string) $payment->id, 'error');
            }

            $payment->user?->notify(new \App\Notifications\GeneralNotification(
                'Платёж обрабатывается',
                'Платёж принят и обрабатывается. Баланс будет пополнен после подтверждения банка.',
                'info',
                route('dashboard.billing'),
                'Перейти к финансам'
            ));
        }

        return redirect()->route('dashboard.billing')->with('success', 'Платеж обрабатывается. Баланс будет пополнен в ближайшее время.');
    }

    public function failed(Request $request)
    {
        return redirect()->route('dashboard.billing')->with('error', 'Оплата не была завершена.');
    }

    public function webhook(Request $request)
    {
        $data = $request->all();

        $sanitized = $data;
        if (isset($sanitized['Token'])) {
            $sanitized['Token'] = '***';
        }
        Log::info('TBank Webhook received', $sanitized);

        // Extract OrderId
        $orderId = $data['OrderId'] ?? null;
        if (! $orderId) {
            return response('Invalid OrderId', 400);
        }

        // Validate Token signature
        // T-Bank sends Token in request. We need to verify it.
        // We MUST verify signature before processing money!
        if (! $this->tbankService->validateCallback($data)) {
            Log::warning('TBank Webhook: Invalid Token', $sanitized);

            $eventHash = hash('sha256', json_encode($sanitized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $event = TBankWebhookEvent::firstOrCreate(
                ['event_hash' => $eventHash],
                [
                    'order_id' => $orderId,
                    'provider_payment_id' => $data['PaymentId'] ?? null,
                    'status' => $data['Status'] ?? null,
                    'signature_valid' => false,
                    'payload' => $sanitized,
                ]
            );

            if (! $event->processed_at) {
                $event->update([
                    'processed_at' => now(),
                    'process_result' => 'rejected',
                    'error_message' => 'Invalid signature',
                ]);
            }

            return response('Invalid Token', 400);
        }

        $eventHash = hash('sha256', json_encode($sanitized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $event = TBankWebhookEvent::firstOrCreate(
            ['event_hash' => $eventHash],
            [
                'order_id' => $orderId,
                'provider_payment_id' => $data['PaymentId'] ?? null,
                'status' => $data['Status'] ?? null,
                'signature_valid' => true,
                'payload' => $sanitized,
            ]
        );

        if (! $event->processed_at) {
            try {
                ProcessTBankWebhookEvent::dispatchSync($event->id);
            } catch (\Throwable $e) {
                $this->auditLogger->log('payment_webhook_process_failed', ['error' => $e->getMessage()], 'payment', (string) ($event->order_id ?? ''), 'error');
                throw $e;
            }
        }

        return response('OK', 200);
    }
}
