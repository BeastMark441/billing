<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TBankService
{
    protected $terminalKey;

    protected $password;

    protected $baseUrl;

    protected bool $verifySsl;

    public function __construct()
    {
        $this->terminalKey = config('services.tbank.terminal_key');
        $this->password = config('services.tbank.password');
        $this->baseUrl = (string) config('services.tbank.url');
        $this->verifySsl = (bool) config('services.tbank.verify_ssl', true);

        if ($this->baseUrl !== '' && ! str_ends_with($this->baseUrl, '/')) {
            $this->baseUrl .= '/';
        }
    }

    /**
     * Initialize payment
     */
    public function init(Payment $payment)
    {
        if (! $this->terminalKey || ! $this->password || $this->baseUrl === '') {
            throw new \Exception('Платёжная система T-Bank не настроена. Проверьте параметры в .env (TerminalKey, Password, URL).');
        }

        // Amount in kopecks (cents)
        $amountKopecks = (int) ($payment->amount * 100);

        $params = [
            'TerminalKey' => $this->terminalKey,
            'Amount' => $amountKopecks,
            'OrderId' => $payment->id.'_'.time(), // Unique order ID
            'Description' => 'Пополнение баланса NODEUM',
            'SuccessURL' => route('payments.success'),
            'FailURL' => route('payments.failed'),
            'NotificationURL' => (string) (config('services.tbank.webhook_url') ?: route('payments.webhook')),
        ];

        // Add Receipt only if taxation system is set (optional for simple transfers but required for fiscalization)
        // For testing/demo we might skip it or include minimal data.
        // Important: DATA and Receipt should be encoded in JSON if passed, BUT T-Bank API expects them as nested arrays in JSON body request.
        // However, for Token generation, they are excluded.

        $receipt = [
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

        $params['Receipt'] = $receipt;
        $params['DATA'] = ['Email' => $payment->user->email];

        // Generate token (without Receipt and DATA)
        $params['Token'] = $this->generateToken($params);

        // Send JSON request
        $response = Http::asJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->timeout(12)
            ->retry(2, 250)
            ->post($this->baseUrl.'Init', $params);

        if (! $response->successful() || ! $response->json('Success')) {
            Log::error('TBank Init Error', ['response' => $response->json(), 'payment_id' => $payment->id]);
            throw new \Exception('Ошибка инициализации платежа: '.($response->json('Message') ?? 'Unknown error').' ('.($response->json('Details') ?? '').')');
        }

        $data = $response->json();

        if (! isset($data['PaymentURL']) || ! is_string($data['PaymentURL']) || $data['PaymentURL'] === '') {
            throw new \Exception('T-Bank вернул пустую ссылку на оплату.');
        }

        $payment->update([
            'payment_id' => $data['PaymentId'] ?? null,
            'payment_url' => $data['PaymentURL'],
            'status' => 'pending',
            'payload' => array_merge($payment->payload ?? [], ['init_response' => $data]),
        ]);

        return $data['PaymentURL'];
    }

    /**
     * Check payment status
     */
    public function getState($paymentId)
    {
        $params = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId' => $paymentId,
        ];

        $params['Token'] = $this->generateToken($params);

        $response = Http::withOptions(['verify' => $this->verifySsl])
            ->timeout(12)
            ->retry(2, 250)
            ->post($this->baseUrl.'GetState', $params);

        return $response->json();
    }

    /**
     * Generate signature token
     */
    protected function generateToken(array $params)
    {
        $tokenParams = $params;
        unset($tokenParams['Token']);
        unset($tokenParams['Receipt']);
        unset($tokenParams['DATA']);
        $tokenParams['Password'] = $this->password;

        ksort($tokenParams);

        $values = '';
        foreach ($tokenParams as $value) {
            if (is_array($value) || is_object($value)) {
                $values .= json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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

    public function validateCallback(array $data)
    {
        $token = $data['Token'] ?? '';
        unset($data['Token']);

        $generatedToken = $this->generateToken($data);

        return $token === $generatedToken;
    }
}
