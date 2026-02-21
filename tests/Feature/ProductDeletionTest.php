<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Server;
use App\Models\Node;

uses(RefreshDatabase::class);

it('prevents deleting product with active servers', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $node = Node::create(['name' => 'n1', 'ptero_id' => 1, 'ip' => '127.0.0.1', 'port_range_start' => 25565, 'port_range_end' => 25600, 'is_active' => true]);
    $product = Product::create([
        'name' => 'Plan A',
        'type' => 'game',
        'price_monthly' => 100,
        'resources' => ['cpu' => 100, 'ram' => 1024, 'disk' => 10240, 'ports' => 1, 'egg_id' => 1, 'nest_id' => 1],
        'is_active' => true,
    ]);
    Server::create([
        'user_id' => $admin->id,
        'product_id' => $product->id,
        'node_id' => $node->id,
        'ptero_server_id' => 1,
        'identifier' => 'abcd',
        'name' => 'srv',
        'ip' => '127.0.0.1',
        'port' => 25565,
        'status' => 'active',
        'expires_at' => now()->addMonth(),
    ]);
    $this->actingAs($admin);
    $res = $this->deleteJson("/api/admin/products/{$product->id}");
    $res->assertStatus(409)->assertJson(['code' => 'PRODUCT_IN_USE']);
});
