<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Services\TBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $tbank;

    public function __construct(TBankService $tbank)
    {
        $this->tbank = $tbank;
    }

    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
        ]);

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'gateway' => 'tbank',
            'status' => 'pending',
        ]);

        try {
            $url = $this->tbank->initPayment($payment);
            return response()->json(['url' => $url]);
        } catch (\InvalidArgumentException $e) {
            \Log::error('Payment init validation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            \Log::error('Payment init failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('TBank callback received', [
            'OrderId' => $request->OrderId,
            'Status' => $request->Status,
            'PaymentId' => $request->PaymentId,
            'Amount' => $request->Amount,
        ]);
        
        try {
            if (!$this->tbank->checkCallback($request->all())) {
                Log::warning('TBank callback token verification failed', [
                    'OrderId' => $request->OrderId,
                    'remote_ip' => $request->ip()
                ]);
                return response('Invalid Token', 400);
            }
        } catch (\Throwable $e) {
            Log::error('TBank callback verification error', ['error' => $e->getMessage()]);
            return response('Verification Error', 400);
        }

        $payment = Payment::find($request->OrderId);
        
        if (!$payment) {
            Log::warning('Payment not found for callback', ['OrderId' => $request->OrderId]);
            return response('Payment not found', 404);
        }

        if ($request->Status === 'CONFIRMED') {
            if ($payment->status === 'completed') {
                Log::info('Payment already completed, skipping', ['payment_id' => $payment->id]);
                return response('OK', 200);
            }
            try {
                DB::transaction(function () use ($payment, $request) {
                    $payment->status = 'completed';
                    $payment->transaction_id = $request->PaymentId;
                    $payment->save();

                    if ($payment->order_id) {
                        // Order-linked payment: mark order paid and provision server
                        $order = Order::find($payment->order_id);
                        if ($order && $order->status !== 'paid') {
                            $order->status = 'paid';
                            $order->save();
                            app(\App\Services\ServerProvisioningService::class)->provision($order);
                        }
                        Log::info('Order payment completed and server provisioning triggered', [
                            'payment_id' => $payment->id,
                            'order_id' => $payment->order_id
                        ]);
                    } else {
                        // Plain top-up
                        $user = User::find($payment->user_id);
                        $before = $user->balance;
                        $user->increment('balance', $payment->amount);
                        Log::info('User balance topped up', [
                            'user_id' => $user->id,
                            'payment_id' => $payment->id,
                            'old_balance' => $before,
                            'amount' => $payment->amount,
                            'new_balance' => $before + $payment->amount,
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Payment callback processing failed', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
                return response('Processing Error', 500);
            }
        } elseif ($request->Status === 'REJECTED') {
            $payment->status = 'failed';
            $payment->save();
            Log::warning('Payment rejected', ['payment_id' => $payment->id]);
        }

        return response('OK', 200);
    }
}
