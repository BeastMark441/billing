<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Product;
use App\Models\Node;
use App\Services\PterodactylService;

uses(RefreshDatabase::class);

class PteroStub2 extends PterodactylService {
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

it('fails with 402 when balance is insufficient', function () {
    App::bind(PterodactylService::class, fn() => new PteroStub2());
    Config::set('services.pterodactyl.is_pelican', true);
    $user = User::factory()->create(['balance' => 50]);
    Sanctum::actingAs($user);
    Node::create(['name' => 'n1', 'ptero_id' => 1, 'ip' => '127.0.0.1', 'port_range_start' => 25565, 'port_range_end' => 25600, 'is_active' => true]);
    $product = Product::create([
        'name' => 'Plan A',
        'type' => 'game',
        'price_monthly' => 100,
        'resources' => ['cpu' => 100, 'ram' => 1024, 'disk' => 10240, 'ports' => 1, 'egg_id' => 1],
        'is_active' => true,
    ]);
    $res = $this->postJson('/api/client/orders', ['product_id' => $product->id]);
    $res->assertStatus(402)->assertJsonStructure(['error']);
});
