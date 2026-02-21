<?php

namespace App\Services;

use App\Models\Node;
use App\Models\Order;
use App\Models\Server;
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

        // 1. Find Node
        $node = $this->findBestNode($product);
        if (!$node) {
            throw new Exception('No available nodes found');
        }

        // 2. Allocate Ports
        $allocations = $this->ptero->getAllocations($node->ptero_id);
        $freeAllocation = $this->findFreeAllocation($allocations);
        
        if (!$freeAllocation) {
             throw new Exception('No free allocations on node ' . $node->name);
        }

        // Check/Create User in Pterodactyl
        if (!$user->ptero_id) {
            $pteroUser = $this->ptero->createUser([
                'email' => $user->email,
                'username' => 'user_' . $user->id,
                'first_name' => $user->name,
                'last_name' => 'User',
            ]);
            $user->ptero_id = $pteroUser['id'];
            $user->save();
        }

        // 3. Create Server on Pterodactyl
        // Resources: cpu, ram, disk, ports
        // We assume product->resources has these keys
        $resources = $product->resources;

        $serverData = [
            'name' => $product->name . ' - ' . $user->name,
            'user' => $user->ptero_id,
            'nest' => $resources['nest_id'] ?? 1, // Default or from product
            'egg' => $resources['egg_id'] ?? 1, // Default or from product
            'docker_image' => $resources['docker_image'] ?? 'ghcr.io/pterodactyl/yolks:java_17',
            'startup' => $resources['startup'] ?? 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar server.jar',
            'environment' => [
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
                'databases' => 0,
                'allocations' => ($resources['ports'] ?? 1) - 1,
                'backups' => 0,
            ],
            'allocation' => [
                'default' => $freeAllocation['id'],
            ],
        ];

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
