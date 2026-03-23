<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use App\Services\TBankApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TBankPaymentSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.tbank.terminal_key' => 'test_terminal']);
        config(['services.tbank.password' => 'test_password']);
        config(['services.tbank.url' => 'https://test-api.tbank.ru/v2/']);
    }

    /** @test */
    public function it_syncs_new_payment_to_confirmed_status()
    {
        $user = User::factory()->create(['balance' => 0]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'payment_id' => '8195457123',
            'amount' => 100,
            'status' => 'new',
        ]);

        Http::fake([
            '*/GetState' => Http::response([
                'Success' => true,
                'Status' => 'CONFIRMED',
                'PaymentId' => '8195457123',
                'OrderId' => $payment->id,
                'Amount' => 10000,
                'ErrorCode' => '0'
            ], 200)
        ]);

        $this->artisan('payments:sync-tbank')
            ->expectsOutput("Syncing 1 payments...")
            ->expectsOutput("Payment #{$payment->id} (ID: 8195457123): Current status is CONFIRMED")
            ->assertExitCode(0);

        $payment->refresh();
        $this->assertEquals('confirmed', $payment->status);
        $this->assertNotNull($payment->credited_at);
        $this->assertEquals(100, $user->refresh()->balance);
    }

    /** @test */
    public function it_increments_sync_attempts_on_failure()
    {
        $user = User::factory()->create();
        $payment = Payment::create([
            'user_id' => $user->id,
            'payment_id' => '8195466105',
            'amount' => 100,
            'status' => 'new',
            'sync_attempts' => 0
        ]);

        Http::fake([
            '*/GetState' => Http::response([
                'Success' => false,
                'ErrorCode' => '99',
                'Message' => 'Internal error'
            ], 200)
        ]);

        $this->artisan('payments:sync-tbank');

        $payment->refresh();
        $this->assertEquals(1, $payment->sync_attempts);
        $this->assertEquals('new', $payment->status);
        $this->assertEquals('T-Bank API Error: Internal error', $payment->error_message);
    }

    /** @test */
    public function it_marks_as_failed_after_three_attempts()
    {
        $user = User::factory()->create();
        $payment = Payment::create([
            'user_id' => $user->id,
            'payment_id' => '8195466105',
            'amount' => 100,
            'status' => 'new',
            'sync_attempts' => 2,
            'last_sync_at' => now()->subMinutes(10)
        ]);

        Http::fake([
            '*/GetState' => Http::response(['Success' => false, 'ErrorCode' => '500'], 200)
        ]);

        $this->artisan('payments:sync-tbank');

        $payment->refresh();
        $this->assertEquals(3, $payment->sync_attempts);
        $this->assertEquals('failed', $payment->status);
    }
}
