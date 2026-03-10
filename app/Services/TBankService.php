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

    public function __construct()
    {
        $this->terminalKey = config('services.tbank.terminal_key');
        $this->password = config('services.tbank.password');
        $this->baseUrl = config('services.tbank.url');
    }

    /**
     * Initialize payment
     */
    public function init(Payment $payment)
    {
        // Amount in kopecks (cents)
        $amountKopecks = (int) ($payment->amount * 100);
        
        $params = [
            'TerminalKey' => $this->terminalKey,
            'Amount' => $amountKopecks,
            'OrderId' => $payment->id . '_' . time(), // Unique order ID
            'Description' => 'Пополнение баланса NODEUM',
            'SuccessURL' => route('payments.success'),
            'FailURL' => route('payments.failed'),
            'NotificationURL' => route('payments.webhook'),
            'DATA' => [
                'Email' => $payment->user->email,
            ],
            'Receipt' => [
                'Email' => $payment->user->email,
                'Taxation' => 'osn', // Simplified taxation system or OSN
                'Items' => [
                    [
                        'Name' => 'Пополнение баланса',
                        'Price' => $amountKopecks,
                        'Quantity' => 1,
                        'Amount' => $amountKopecks,
                        'Tax' => 'none', // No VAT
                    ]
                ]
            ]
        ];

        $params['Token'] = $this->generateToken($params);

        $response = Http::post($this->baseUrl . 'Init', $params);

        if (!$response->successful() || !$response->json('Success')) {
            Log::error('TBank Init Error', ['response' => $response->json(), 'payment_id' => $payment->id]);
            throw new \Exception('Ошибка инициализации платежа: ' . ($response->json('Message') ?? 'Unknown error'));
        }

        $data = $response->json();
        
        $payment->update([
            'payment_id' => $data['PaymentId'],
            'payment_url' => $data['PaymentURL'],
            'status' => 'pending', // Waiting for user to pay
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

        $response = Http::post($this->baseUrl . 'GetState', $params);

        return $response->json();
    }

    /**
     * Generate signature token
     */
    protected function generateToken(array $params)
    {
        // 1. Remove optional params that are not part of token generation if they are arrays/objects
        // T-Bank documentation: "В формировании токена участвуют все параметры, кроме Token"
        // But specifically for objects like Receipt or DATA, they might be excluded or serialized.
        // Usually Init request token is generated WITHOUT Receipt and DATA.
        
        $tokenParams = $params;
        unset($tokenParams['Token']);
        unset($tokenParams['Receipt']);
        unset($tokenParams['DATA']);

        // Add password
        $tokenParams['Password'] = $this->password;

        // Sort by key
        ksort($tokenParams);

        // Concatenate values
        $values = '';
        foreach ($tokenParams as $key => $value) {
            // Only scalar values
            if (!is_array($value) && !is_object($value)) {
                $values .= $value;
            }
        }

        // SHA-256
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