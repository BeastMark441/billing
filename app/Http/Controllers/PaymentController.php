<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\TBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        if (!$this->tbank->checkCallback($request->all())) {
            return response('Invalid Token', 400);
        }

        $payment = Payment::find($request->OrderId);
        
        if (!$payment) {
            return response('Payment not found', 404);
        }

        if ($request->Status === 'CONFIRMED' && $payment->status !== 'completed') {
            $payment->status = 'completed';
            $payment->transaction_id = $request->PaymentId;
            $payment->save();

            // Top up user balance
            $user = User::find($payment->user_id);
            $user->increment('balance', $payment->amount);
        } elseif ($request->Status === 'REJECTED') {
            $payment->status = 'failed';
            $payment->save();
        }

        return response('OK', 200);
    }
}
