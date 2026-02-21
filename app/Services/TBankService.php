<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Payment;
use App\Models\User;
use Exception;

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

        $response = Http::post($this->url . '/Init', $data);

        if ($response->failed() || !$response['Success']) {
            throw new Exception('TBank Init Failed: ' . ($response['Message'] ?? 'Unknown error'));
        }

        return $response['PaymentURL'];
    }

    /**
     * Check Token from Webhook
     */
    public function checkCallback($data)
    {
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
