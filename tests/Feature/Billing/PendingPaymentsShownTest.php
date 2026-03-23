<?php

namespace Tests\Feature\Billing;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendingPaymentsShownTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_payments_are_visible_on_billing_page(): void
    {
        $user = User::factory()->create(['balance' => 0]);

        Payment::create([
            'user_id' => $user->id,
            'amount' => 500,
            'status' => 'pending',
            'description' => 'Пополнение баланса',
            'payment_id' => '12345',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.billing'))
            ->assertOk()
            ->assertSee('Платежи в обработке');
    }
}
