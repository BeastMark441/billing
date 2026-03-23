<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TBankApiService
{
    protected string $terminalKey;
    protected string $password;
    protected string $baseUrl;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->terminalKey = (string) config('services.tbank.terminal_key');
        $this->password = (string) config('services.tbank.password');
        $this->baseUrl = (string) config('services.tbank.url');
        $this->verifySsl = (bool) config('services.tbank.verify_ssl', true);

        if ($this->baseUrl !== '' && ! str_ends_with($this->baseUrl, '/')) {
            $this->baseUrl .= '/';
        }
    }

    /**
     * Create a payment link using Acquiring (Merchant) Init method
     */
    public function createPaymentLink(Payment $payment): string
    {
        if (!$this->terminalKey || !$this->password) {
            throw new \Exception('Платёжная система T-Bank не настроена. Проверьте TerminalKey и Password в .env.');
        }

        // Amount in kopecks (cents)
        $amountKopecks = (int) ($payment->amount * 100);

        $params = [
            'TerminalKey' => $this->terminalKey,
            'Amount' => $amountKopecks,
            'OrderId' => $payment->id . '_' . time(),
            'Description' => 'Пополнение баланса NODEUM',
            'SuccessURL' => route('payments.success'),
            'FailURL' => route('payments.failed'),
            'NotificationURL' => (string) (config('services.tbank.webhook_url') ?: route('payments.webhook')),
        ];

        // Add receipt for fiscalization
        $params['Receipt'] = [
            'Email' => $payment->user->email,
            'Taxation' => 'osn',
            'Items' => [
                [
                    'Name' => 'Пополнение баланса',
                    'Price' => $amountKopecks,
                    'Quantity' => 1,
                    'Amount' => $amountKopecks,
                    'Tax' => 'none',
                ],
            ],
        ];

        $params['DATA'] = ['Email' => $payment->user->email];

        // Generate Token (without Receipt and DATA)
        $params['Token'] = $this->generateToken($params);

        $response = Http::asJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->timeout(12)
            ->retry(2, 250)
            ->post($this->baseUrl . 'Init', $params);

        if (!$response->successful() || !$response->json('Success')) {
            Log::error('TBank Init Error', ['response' => $response->json(), 'payment_id' => $payment->id]);
            throw new \Exception('Ошибка инициализации платежа: ' . ($response->json('Message') ?? 'Unknown error'));
        }

        $data = $response->json();
        $paymentUrl = $data['PaymentURL'] ?? null;

        if (!$paymentUrl) {
            throw new \Exception('T-Bank не вернул ссылку на оплату.');
        }

        $payment->update([
            'payment_id' => $data['PaymentId'] ?? null,
            'payment_url' => $paymentUrl,
            'status' => 'pending',
            'payload' => array_merge($payment->payload ?? [], ['init_response' => $data]),
        ]);

        return $paymentUrl;
    }

    /**
     * Get payment state using GetState
     */
    public function getPaymentState(string $paymentId): array
    {
        $params = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId' => $paymentId,
        ];

        $params['Token'] = $this->generateToken($params);

        $response = Http::asJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->post($this->baseUrl . 'GetState', $params);

        return $response->json();
    }

    /**
     * Verify webhook signature (Token)
     */
    public function verifyWebhook(array $data): bool
    {
        $receivedToken = $data['Token'] ?? '';
        
        // Remove Token for verification calculation
        $verifyData = $data;
        unset($verifyData['Token']);

        $generatedToken = $this->generateToken($verifyData, true);

        if (!hash_equals((string)$receivedToken, $generatedToken)) {
            Log::warning('TBank Webhook Signature Mismatch', [
                'received' => $receivedToken,
                'generated' => $generatedToken,
                'payload' => $data
            ]);
            return false;
        }

        return true;
    }

    /**
     * Generate T-Bank Token (SHA256)
     */
    protected function generateToken(array $params, bool $isWebhook = false): string
    {
        $tokenParams = $params;
        
        // Exclude specific fields from Token generation as per documentation
        unset($tokenParams['Token']);
        unset($tokenParams['Receipt']);
        unset($tokenParams['DATA']);
        
        // For webhooks (Notifications), Success should also be excluded according to some docs
        if ($isWebhook) {
            unset($tokenParams['Success']);
        }
        
        $tokenParams['Password'] = $this->password;

        // Sort by key
        ksort($tokenParams);

        // Concatenate values
        $values = '';
        foreach ($tokenParams as $value) {
            if (is_array($value) || is_object($value)) {
                // Nested structures are ignored in concatenation
                continue;
            }

            if (is_bool($value)) {
                $values .= $value ? 'true' : 'false';
                continue;
            }

            if ($value === null) {
                continue;
            }

            $values .= (string) $value;
        }

        return hash('sha256', $values);
    }

    /**
     * Check idempotency
     */
    public function isIdempotent(array $data): bool
    {
        $paymentId = $data['PaymentId'] ?? null;
        $status = strtoupper($data['Status'] ?? '');

        if (!$paymentId) {
            return false;
        }

        $payment = Payment::where('payment_id', (string)$paymentId)->first();
        if (!$payment) {
            return false;
        }

        // Already confirmed
        if ($payment->credited_at) {
            return true;
        }

        return false;
    }
}
