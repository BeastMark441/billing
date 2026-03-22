<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\InfrastructureService;
use App\Models\InfrastructureCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartLogicTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        
        $category = InfrastructureCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $this->service = InfrastructureService::create([
            'infrastructure_category_id' => $category->id,
            'name' => 'Test Service',
            'slug' => 'test-service',
            'price' => 100.00,
            'description' => 'Test description'
        ]);
    }

    /** @test */
    public function order_in_cart_does_not_create_status_history()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'infrastructure_service_id' => $this->service->id,
            'status' => 'cart',
            'price' => $this->service->price,
        ]);

        $this->assertEquals(0, $order->statusHistory()->count(), 'Order in cart should NOT have status history.');
    }

    /** @test */
    public function order_transition_to_paid_creates_status_history()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'infrastructure_service_id' => $this->service->id,
            'status' => 'cart',
            'price' => $this->service->price,
        ]);

        $order->update(['status' => 'paid']);

        $this->assertEquals(1, $order->statusHistory()->count(), 'Order transition from cart to paid SHOULD create status history.');
        $this->assertEquals('paid', $order->statusHistory->first()->status_to);
    }

    /** @test */
    public function non_cart_order_creation_creates_status_history()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'infrastructure_service_id' => $this->service->id,
            'status' => 'paid',
            'price' => $this->service->price,
        ]);

        $this->assertEquals(1, $order->statusHistory()->count(), 'Directly created paid order SHOULD have status history.');
        $this->assertEquals('paid', $order->statusHistory->first()->status_to);
    }
}
