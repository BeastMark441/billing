<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Product;
use App\Models\Node;
use App\Services\PterodactylService;
use App\Services\TBankService;
use App\Models\Payment;
use App\Models\Order;

uses(RefreshDatabase::class);

class PteroStubGateway extends PterodactylService {
    public function __construct() {}
    public function isPelican(): bool { return true; }
    public function getAllocations($nodeId) { 
        return [['attributes' => ['id' => 1, 'ip' => '127.0.0.1', 'port' => 25565, 'assigned' => false]]];
    }
    public function createServer($data) { 
        return ['id' => 1, 'identifier' => 'abcd', 'name' => $data['name']];
    }
    public function findUserByEmail(string $email): ?array { return ['id' => 1]; }
}

class TBankStub extends TBankService {
    public function __construct() {}
    public function initPayment(Payment $payment) { return 'http://pay.test/' . $payment->id; }
    public function checkCallback($data) { return true; }
}

it('returns payment url when pay_method gateway', function () {
    App::bind(PterodactylService::class, fn() => new PteroStubGateway());
    App::bind(TBankService::class, fn() => new TBankStub());
    Config::set('services.pterodactyl.is_pelican', true);

    $user = User::factory()->create(['balance' => 0]);
    Sanctum::actingAs($user);
    Node::create(['name' => 'n1', 'ptero_id' => 1, 'ip' => '127.0.0.1', 'port_range_start' => 25565, 'port_range_end' => 25600, 'is_active' => true]);
    $product = Product::create([
        'name' => 'Plan G',
        'type' => 'game',
        'price_monthly' => 150,
        'resources' => ['cpu' => 100, 'ram' => 1024, 'disk' => 10240, 'ports' => 1, 'egg_id' => 1],
        'is_active' => true,
    ]);

    $res = $this->postJson('/api/client/orders', ['product_id' => $product->id, 'pay_method' => 'gateway']);
    $res->assertStatus(200)->assertJsonStructure(['url', 'order_id']);
    $order = Order::first();
    expect($order->status)->toBe('awaiting_payment');
});

it('provisions server after confirmed callback for order payment', function () {
    App::bind(PterodactylService::class, fn() => new PteroStubGateway());
    App::bind(TBankService::class, fn() => new TBankStub());
    Config::set('services.pterodactyl.is_pelican', true);

    $user = User::factory()->create(['balance' => 0]);
    Sanctum::actingAs($user);
    Node::create(['name' => 'n1', 'ptero_id' => 1, 'ip' => '127.0.0.1', 'port_range_start' => 25565, 'port_range_end' => 25600, 'is_active' => true]);
    $product = Product::create([
        'name' => 'Plan H',
        'type' => 'game',
        'price_monthly' => 150,
        'resources' => ['cpu' => 100, 'ram' => 1024, 'disk' => 10240, 'ports' => 1, 'egg_id' => 1],
        'is_active' => true,
    ]);

    // Init order with gateway
    $this->postJson('/api/client/orders', ['product_id' => $product->id, 'pay_method' => 'gateway'])
        ->assertStatus(200);
    $payment = Payment::first();

    // Callback confirm
    $this->post('/api/payment/callback', [
        'OrderId' => $payment->id,
        'PaymentId' => 'cb123',
        'Status' => 'CONFIRMED',
        'Token' => 'stub', // ignored by stub checker
    ])->assertStatus(200);

    $order = Order::first();
    $order->refresh();
    expect($order->status)->toBe('paid');
    $this->getJson('/api/client/servers')->assertStatus(200);
});

