<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Models\Payment;
use App\Services\TBankService;

uses(RefreshDatabase::class);

class TBankStubTopup extends TBankService {
    public function __construct() {}
    public function checkCallback($data) { return true; }
}

it('credits user balance on confirmed topup callback', function () {
    App::bind(TBankService::class, fn() => new TBankStubTopup());
    $user = User::factory()->create(['balance' => 0]);
    $payment = Payment::create([
        'user_id' => $user->id,
        'amount' => 100,
        'gateway' => 'tbank',
        'status' => 'pending',
    ]);

    $this->post('/api/payment/callback', [
        'OrderId' => $payment->id,
        'PaymentId' => 'cb456',
        'Status' => 'CONFIRMED',
        'Token' => 'stub',
    ])->assertStatus(200);

    $user->refresh();
    expect($user->balance)->toBe($payment->amount);
});
