<?php

namespace App\Services;

use App\Models\Node;
use App\Models\Order;
use App\Models\Server;
use Illuminate\Support\Facades\Log;
use Exception;

class ServerProvisioningService
{
    protected $ptero;

    public function __construct(PterodactylService $ptero)
    {
        $this->ptero = $ptero;
    }

    public function provision(Order $order)
    {
        $product = $order->product;
        $user = $order->user;

        Log::info('Provisioning started', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // 1. Find Node
        $node = $this->findBestNode($product);
        if (!$node) {
            Log::error('Provisioning failed: no available nodes', ['order_id' => $order->id]);
            throw new Exception('No available nodes found');
        }

        // 2. Allocate Ports
        $allocations = $this->ptero->getAllocations($node->ptero_id);
        $freeAllocation = $this->findFreeAllocation($allocations);
        
        if (!$freeAllocation) {
            Log::error('Provisioning failed: no free allocations', [
                'order_id' => $order->id,
                'node_id' => $node->id
            ]);
            throw new Exception('No free allocations on node ' . $node->name);
        }

        // Check/Create User in Panel
        if (!$user->ptero_id) {
            // Try to find existing by email to avoid duplicate errors
            $existing = $this->ptero->findUserByEmail($user->email);
            if ($existing) {
                $user->ptero_id = $existing['id'];
                $user->save();
                Log::info('Linked existing panel user by email', [
                    'order_id' => $order->id,
                    'panel_user_id' => $existing['id'],
                ]);
            } else {
                $pteroUser = $this->ptero->createUser([
                    'email' => $user->email,
                    'username' => 'user_' . $user->id,
                    'first_name' => $user->name,
                    'last_name' => 'User',
                ]);
                $user->ptero_id = $pteroUser['id'];
                $user->save();
                Log::info('Created new panel user', [
                    'order_id' => $order->id,
                    'panel_user_id' => $pteroUser['id'],
                ]);
            }
        }

        // 3. Create Server on Pterodactyl
        // Resources: cpu, ram, disk, ports
        // We assume product->resources has these keys
        $resources = $product->resources;

        foreach (['cpu', 'ram', 'disk'] as $key) {
            if (!isset($resources[$key])) {
                Log::error('Provisioning failed: missing resource key', [
                    'order_id' => $order->id,
                    'missing' => $key,
                ]);
                throw new Exception('Product resources missing required key: ' . $key);
            }
        }

        $serverData = [
            'name' => $product->name . ' - ' . $user->name,
            'user' => $user->ptero_id,
            'docker_image' => $resources['docker_image'] ?? 'ghcr.io/pterodactyl/yolks:java_17',
            'startup' => $resources['startup'] ?? 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar server.jar',
            'environment' => $resources['environment'] ?? [
                'SERVER_JARFILE' => 'server.jar',
                'VANILLA_VERSION' => 'latest',
            ],
            'limits' => [
                'memory' => $resources['ram'],
                'swap' => 0,
                'disk' => $resources['disk'],
                'io' => 500,
                'cpu' => $resources['cpu'],
            ],
            'feature_limits' => [
                'databases' => $resources['databases'] ?? 0,
                'allocations' => max(0, ($resources['ports'] ?? 1) - 1),
                'backups' => $resources['backups'] ?? 0,
            ],
            'allocation' => [
                'default' => $freeAllocation['id'],
            ],
        ];

        if (!$this->ptero->isPelican()) {
            if (!isset($resources['egg_id']) || !isset($resources['nest_id'])) {
                Log::error('Provisioning failed: missing egg/nest for Pterodactyl mode', [
                    'order_id' => $order->id,
                ]);
                throw new Exception('Missing egg/nest configuration for server creation');
            }
            $serverData['nest'] = $resources['nest_id'];
            $serverData['egg'] = $resources['egg_id'];
        } else {
            if (!isset($resources['egg_id'])) {
                Log::error('Provisioning failed: missing egg for Pelican mode', [
                    'order_id' => $order->id,
                ]);
                throw new Exception('Missing egg configuration for server creation (Pelican mode)');
            }
            $serverData['egg'] = $resources['egg_id'];
        }

        Log::info('Creating panel server', [
            'order_id' => $order->id,
            'payload_keys' => array_keys($serverData),
        ]);
        $pteroServer = $this->ptero->createServer($serverData);

        // 4. Save Server in DB
        $server = Server::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'node_id' => $node->id,
            'ptero_server_id' => $pteroServer['id'],
            'identifier' => $pteroServer['identifier'],
            'name' => $pteroServer['name'],
            'ip' => $freeAllocation['ip'],
            'port' => $freeAllocation['port'],
            'status' => 'active',
            'expires_at' => now()->addMonth(),
        ]);

        Log::info('Provisioning completed', [
            'order_id' => $order->id,
            'server_id' => $server->id,
        ]);

        return $server;
    }

    protected function findBestNode($product)
    {
        // Logic to find best node. For MVP, just pick first active.
        return Node::where('is_active', true)->first();
    }
    
    protected function findFreeAllocation($allocations)
    {
        foreach ($allocations as $allocation) {
            if (!$allocation['attributes']['assigned']) {
                return $allocation['attributes'];
            }
        }
        return null;
    }
}
