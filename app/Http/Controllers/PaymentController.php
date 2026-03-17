<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTBankWebhookEvent;
use App\Models\TBankWebhookEvent;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $tbankService;

    public function __construct(\App\Services\TBankService $tbankService, protected AuditLogger $auditLogger)
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
        // User redirected back from T-Bank
        // Usually we just show "Success" message, real status update comes via Webhook
        // But we can check status manually just in case
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
            ProcessTBankWebhookEvent::dispatch($event->id);
        }

        return response('OK', 200);
    }
}
