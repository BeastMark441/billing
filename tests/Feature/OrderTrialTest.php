<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Product;
use App\Models\Trial;
use App\Models\Node;
use App\Services\PterodactylService;

uses(RefreshDatabase::class);

class PteroStub extends PterodactylService {
    public function __construct() {}
    public function isPelican(): bool { return true; }
    public function getAllocations($nodeId) { 
        return [['attributes' => ['id' => 1, 'ip' => '127.0.0.1', 'port' => 25565, 'assigned' => false]]];
    }
    public function createServer($data) { 
        if (!array_key_exists('environment', $data)) {
            throw new Exception('environment missing');
        }
        return ['id' => 1, 'identifier' => 'abcd', 'name' => $data['name']];
    }
    public function findUserByEmail(string $email): ?array { return ['id' => 1]; }
}

it('creates trial order and sets server expiration', function () {
    App::bind(PterodactylService::class, fn() => new PteroStub());
    Config::set('services.pterodactyl.is_pelican', true);
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $node = Node::create(['name' => 'n1', 'ptero_id' => 1, 'ip' => '127.0.0.1', 'port_range_start' => 25565, 'port_range_end' => 25600, 'is_active' => true]);
    $product = Product::create([
        'name' => 'Plan A',
        'type' => 'game',
        'price_monthly' => 100,
        'resources' => ['cpu' => 100, 'ram' => 1024, 'disk' => 10240, 'ports' => 1, 'egg_id' => 1],
        'is_active' => true,
    ]);
    Trial::create(['product_id' => $product->id, 'duration_days' => 3, 'max_per_user' => 1, 'active' => true]);
    $res = $this->postJson('/api/client/orders', ['product_id' => $product->id, 'use_trial' => true]);
    $res->assertStatus(201)->assertJsonStructure(['server' => ['id']]);
});
