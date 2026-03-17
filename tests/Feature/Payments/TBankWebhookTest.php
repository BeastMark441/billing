<?php

namespace Tests\Feature\Payments;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TBankWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function makeToken(array $payload, string $password): string
    {
        $tokenParams = $payload;
        unset($tokenParams['Token']);
        $tokenParams['Password'] = $password;
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

    public function test_webhook_credits_balance_once(): void
    {
        config([
            'services.tbank.terminal_key' => 'T123',
            'services.tbank.password' => 'P123',
        ]);

        $user = User::factory()->create(['balance' => 0]);
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => 1000,
            'status' => 'pending',
        ]);

        $payload = [
            'TerminalKey' => 'T123',
            'OrderId' => $payment->id.'_'.time(),
            'PaymentId' => '999999',
            'Status' => 'CONFIRMED',
            'Success' => true,
            'Amount' => 100000,
        ];

        $payload['Token'] = $this->makeToken($payload, 'P123');

        $this->post('/payments/webhook', $payload)->assertOk();
        $this->post('/payments/webhook', $payload)->assertOk();

        $user->refresh();
        $payment->refresh();

        $this->assertSame('confirmed', $payment->status);
        $this->assertNotNull($payment->credited_at);
        $this->assertEquals(1000, (float) $user->balance);
        $this->assertDatabaseCount('balance_logs', 1);
    }
}
