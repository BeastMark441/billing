<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\User;
use Exception;
use InvalidArgumentException;

class TBankService
{
    protected $terminalKey;
    protected $password;
    protected $url = 'https://securepay.tinkoff.ru/v2';

    public function __construct()
    {
        $this->terminalKey = config('services.tbank.terminal_key');
        $this->password = config('services.tbank.password');
    }

    /**
     * Init Payment
     */
    public function initPayment(Payment $payment)
    {
        if (empty($this->terminalKey) || empty($this->password)) {
            throw new InvalidArgumentException('Missing TBank configuration (terminal key or password)');
        }
        $data = [
            'TerminalKey' => $this->terminalKey,
            'Amount' => $payment->amount * 100, // In kopecks
            'OrderId' => $payment->id,
            'Description' => "Пополнение баланса Nodeum #{$payment->id}",
            'SuccessURL' => config('app.url') . '/dashboard?payment=success',
            'FailURL' => config('app.url') . '/dashboard?payment=fail',
            'NotificationURL' => config('app.url') . '/api/payment/callback',
        ];

        $data['Token'] = $this->generateToken($data);

        Log::info('TBank Init request', [
            'payment_id' => $payment->id,
            'payload' => array_merge($data, ['Password' => '***']),
        ]);

        $response = Http::post($this->url . '/Init', $data);

        if ($response->failed() || !$response['Success']) {
            Log::error('TBank Init failed', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $message = $response['Message'] ?? ($response['Details'] ?? 'Unknown error');
            throw new Exception('TBank Init Failed: ' . $message);
        }

        if (isset($response['PaymentId'])) {
            $payment->update(['transaction_id' => $response['PaymentId']]);
        }

        Log::info('TBank Init success', [
            'payment_id' => $payment->id,
            'payment_url' => $response['PaymentURL'] ?? null,
        ]);
        return $response['PaymentURL'];
    }

    /**
     * Get Payment State
     */
    public function getState($paymentId)
    {
        if (empty($this->terminalKey) || empty($this->password)) {
            throw new InvalidArgumentException('Missing TBank configuration');
        }

        $data = [
            'TerminalKey' => $this->terminalKey,
            'PaymentId' => $paymentId,
        ];
        
        $data['Token'] = $this->generateToken($data);
        
        $response = Http::post($this->url . '/GetState', $data);
        
        if ($response->failed() || !$response['Success']) {
            throw new Exception('TBank GetState Failed: ' . ($response['Message'] ?? 'Unknown error'));
        }
        
        return $response['Status'];
    }

    /**
     * Check Token from Webhook
     */
    public function checkCallback($data)
    {
        if (empty($data['Token'])) {
            return false;
        }
        $token = $data['Token'];
        unset($data['Token']);
        
        if ($this->generateToken($data) !== $token) {
            return false;
        }
        
        return true;
    }

    protected function generateToken($data)
    {
        $data['Password'] = $this->password;
        ksort($data);
        
        $values = '';
        foreach ($data as $key => $value) {
            if ($key !== 'Token') {
                $values .= $value;
            }
        }
        
        return hash('sha256', $values);
    }
}
