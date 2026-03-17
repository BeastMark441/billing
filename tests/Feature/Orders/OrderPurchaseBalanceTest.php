<?php

namespace Tests\Feature\Orders;

use App\Models\InfrastructureCategory;
use App\Models\InfrastructureService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPurchaseBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_purchase_decreases_balance_and_creates_balance_log(): void
    {
        $user = User::factory()->create([
            'balance' => 1000,
        ]);

        $category = InfrastructureCategory::create([
            'name' => 'VDS',
            'slug' => 'vds',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $service = InfrastructureService::create([
            'infrastructure_category_id' => $category->id,
            'infrastructure_subcategory_id' => null,
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'description' => 'Test',
            'price' => 250,
            'specifications' => [],
            'sort_order' => 1,
            'is_active' => true,
            'one_per_user' => false,
        ]);

        $response = $this->actingAs($user)
            ->post(route('orders.store', $service), []);

        $response->assertRedirect();

        $this->assertSame('750.00', $user->fresh()->balance);

        $this->assertDatabaseHas('balance_logs', [
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => '-250.00',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'infrastructure_service_id' => $service->id,
        ]);

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertNotNull($order->paid_at);
        $this->assertNotNull($order->expires_at);
    }
}
