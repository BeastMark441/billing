<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PterodactylService
{
    protected $baseUrl;

    protected $clientApiKey;

    protected $appApiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.pterodactyl.url');
        $this->clientApiKey = config('services.pterodactyl.client_key');
        $this->appApiKey = config('services.pterodactyl.app_key');
    }

    /**
     * Provision a server for a paid order
     */
    public function provisionServer(Order $order)
    {
        try {
            // 1. Check or Create Pterodactyl User
            $pterodactylUserId = $this->ensureUserExists($order->user);

            // 2. Get Service Configuration
            $service = $order->service;
            $specs = $service->specifications ?? [];

            // Validate required specs
            $this->validateSpecs($specs);

            // 3. Determine Node
            $nodeId = $this->determineNode($specs);

            // 4. Create Server
            $serverData = $this->prepareServerData($order, $pterodactylUserId, $nodeId, $specs);
            $response = $this->createPterodactylServer($serverData);

            // 5. Update Order
            $attributes = $response['attributes'];
            
            // Extract Allocation from Relationships
            // Usually in attributes.relationships.allocations.data[0].attributes
            $allocation = null;
            if (isset($attributes['relationships']['allocations']['data'][0]['attributes'])) {
                $allocation = $attributes['relationships']['allocations']['data'][0]['attributes'];
            } elseif (isset($attributes['allocation'])) {
                // Fallback if structure is different
                $allocation = $attributes['allocation'];
            }

            $order->update([
                'status' => 'active',
                'pterodactyl_server_id' => $attributes['id'],
                'pterodactyl_server_identifier' => $attributes['identifier'],
                'server_ip' => $allocation ? ($allocation['ip_alias'] ?? $allocation['ip']) : '0.0.0.0',
                'server_port' => $allocation ? $allocation['port'] : null,
            ]);

            return true;

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Pterodactyl Provisioning Error: '.$e->getMessage());
            $order->update([
                'status' => 'failed',
                'last_error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getEggDetails(int $eggId)
    {
        // First we need to find the nest for this egg or just search for the egg directly if API supports it
        // Pterodactyl API structure: /api/application/nests/{nest}/eggs/{egg}
        // Since we only have egg_id, we might need to search or iterate nests. 
        // A common trick is to try to get the egg directly if we knew the nest, but we don't.
        // However, usually we can get all eggs from all nests and find it.
        
        // Optimization: Pterodactyl allows getting egg with `include=nest` if we know the endpoint
        // But standard endpoint is nested. Let's try to find it by iterating nests if needed, 
        // OR if the user provides nest_id. 
        // For better UX, let's assume we fetch all nests and their eggs to find the match.
        
        $nestsResponse = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->get($this->baseUrl . '/api/application/nests?include=eggs');
            
        if (!$nestsResponse->successful()) {
            return null;
        }
        
        foreach ($nestsResponse->json()['data'] as $nest) {
            if (isset($nest['attributes']['relationships']['eggs']['data'])) {
                foreach ($nest['attributes']['relationships']['eggs']['data'] as $egg) {
                    if ($egg['attributes']['id'] == $eggId) {
                        return [
                            'docker_image' => $egg['attributes']['docker_image'],
                            'startup' => $egg['attributes']['startup'],
                            'nest_id' => $nest['attributes']['id']
                        ];
                    }
                }
            }
        }
        
        return null;
    }

    protected function ensureUserExists(User $user)
    {
        if ($user->pterodactyl_id) {
            // Validate if user really exists in Pterodactyl, if not, clear ID and recreate
            $response = Http::withToken($this->appApiKey)
                ->withoutVerifying()
                ->acceptJson()
                ->get($this->baseUrl.'/api/application/users/'.$user->pterodactyl_id);
                
            if ($response->successful()) {
                return $user->pterodactyl_id;
            }
            
            // If 404, user was deleted in panel but exists in billing. Clear ID and proceed to create/find.
            $user->update(['pterodactyl_id' => null]);
        }

        // Search by email first
        $response = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->get($this->baseUrl.'/api/application/users', [
                'filter[email]' => $user->email,
            ]);

        if ($response->successful() && ! empty($response['data'])) {
            $pterodactylUser = $response['data'][0]['attributes'];
            $user->update(['pterodactyl_id' => $pterodactylUser['id']]);

            return $pterodactylUser['id'];
        }

        // Create new user
        $password = Str::random(16);
        $response = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->post($this->baseUrl.'/api/application/users', [
                'email' => $user->email,
                'username' => $this->generateUsername($user->name),
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'User',
                'password' => $password,
                'language' => 'en',
            ]);

        if (! $response->successful()) {
            throw new Exception('Failed to create Pterodactyl user: '.$response->body());
        }

        $pterodactylUser = $response->json();
        $user->update(['pterodactyl_id' => $pterodactylUser['attributes']['id']]);

        // Notify user about password (implementation pending)
        // Mail::to($user)->send(new PterodactylAccountCreated($password));

        return $pterodactylUser['attributes']['id'];
    }

    protected function generateUsername($name)
    {
        $slug = Str::slug($name);

        return substr($slug, 0, 8).Str::random(4);
    }

    protected function determineNode($specs)
    {
        // If user selected a node in payload (not implemented yet), use it
        // Otherwise, auto-select
        // For now, simple logic: get all nodes, pick first active one or specific one from specs

        if (isset($specs['node_id'])) {
            return $specs['node_id'];
        }

        // Fetch nodes logic here... for now return 1 or throw
        // Real implementation would check resources
        return 1;
    }

    protected function prepareServerData(Order $order, $userId, $nodeId, $specs)
    {
        // Auto-fetch Egg Details if missing in specs
        $eggDetails = $this->getEggDetails((int) ($specs['egg_id'] ?? 1));
        
        $dockerImage = $specs['docker_image'] ?? $eggDetails['docker_image'] ?? 'quay.io/pterodactyl/core:java';
        $startup = $specs['startup'] ?? $eggDetails['startup'] ?? 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}';
        $eggId = (int) ($specs['egg_id'] ?? 1);
        $nestId = (int) ($specs['nest_id'] ?? $eggDetails['nest_id'] ?? 1); // We don't strictly need nest_id for server creation usually, but good to have

        return [
            'name' => $order->service->name.' #'.$order->id,
            'user' => (int) $userId,
            'egg' => $eggId,
            'docker_image' => $dockerImage,
            'startup' => $startup,
            'environment' => $specs['environment'] ?? [
                'MINECRAFT_VERSION' => 'latest',
                'SERVER_JARFILE' => 'server.jar',
                'BUILD_NUMBER' => 'latest',
            ],
            'limits' => [
                'memory' => (int) ($specs['memory'] ?? 1024),
                'swap' => (int) ($specs['swap'] ?? 0),
                'disk' => (int) ($specs['disk'] ?? 1024),
                'io' => (int) ($specs['io'] ?? 500),
                'cpu' => (int) ($specs['cpu'] ?? 100),
            ],
            'feature_limits' => [
                'databases' => (int) ($specs['databases'] ?? 0),
                'allocations' => (int) ($specs['allocations'] ?? 0),
                'backups' => (int) ($specs['backups'] ?? 0),
            ],
            'allocation' => [
                'default' => $this->findFreeAllocation($nodeId),
            ],
        ];
    }

    protected function findFreeAllocation($nodeId)
    {
        // Try to find a free allocation on the specified node
        // Pterodactyl API: /api/application/nodes/{node}/allocations
        // We need to iterate pages to find one where 'assigned' is false
        
        $page = 1;
        while ($page <= 5) { // Limit pages to prevent infinite loops
            $response = Http::withToken($this->appApiKey)
                ->withoutVerifying()
                ->acceptJson()
                ->get($this->baseUrl . "/api/application/nodes/{$nodeId}/allocations?page={$page}");
                
            if (!$response->successful()) {
                // If we can't fetch allocations, maybe fallback or throw? 
                // Let's assume we can't find one and throw exception
                throw new Exception('Failed to fetch allocations from node ' . $nodeId);
            }
            
            $data = $response->json();
            
            foreach ($data['data'] as $allocation) {
                if (!$allocation['attributes']['assigned']) {
                    return $allocation['attributes']['id'];
                }
            }
            
            if ($data['meta']['pagination']['current_page'] >= $data['meta']['pagination']['total_pages']) {
                break;
            }
            
            $page++;
        }
        
        throw new Exception('No free allocations found on node ' . $nodeId);
    }

    protected function createPterodactylServer($data)
    {
        $response = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->post($this->baseUrl.'/api/application/servers', $data);

        if (! $response->successful()) {
            throw new Exception('Failed to create server: '.$response->body());
        }

        return $response->json();
    }

    public function getServerDetails($serverId)
    {
        $response = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->get($this->baseUrl . '/api/application/servers/' . $serverId);

        if (!$response->successful()) {
            return null;
        }

        return $response->json()['attributes'];
    }

    public function suspendServer($serverId)
    {
        $response = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->post($this->baseUrl . '/api/application/servers/' . $serverId . '/suspend');

        if (!$response->successful()) {
            throw new Exception('Failed to suspend server: ' . $response->body());
        }

        return true;
    }

    public function unsuspendServer($serverId)
    {
        $response = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->post($this->baseUrl . '/api/application/servers/' . $serverId . '/unsuspend');

        if (!$response->successful()) {
            throw new Exception('Failed to unsuspend server: ' . $response->body());
        }

        return true;
    }

    public function deleteServer($serverId)
    {
        $response = Http::withToken($this->appApiKey)
            ->withoutVerifying()
            ->acceptJson()
            ->delete($this->baseUrl . '/api/application/servers/' . $serverId);

        if (!$response->successful()) {
            throw new Exception('Failed to delete server: ' . $response->body());
        }

        return true;
    }

    protected function validateSpecs($specs)
    {
        $required = ['egg_id', 'memory', 'disk', 'cpu'];
        foreach ($required as $field) {
            if (! isset($specs[$field])) {
                throw new Exception("Missing required specification: {$field}");
            }
        }
    }

    protected function getAllocationIp($allocation)
    {
        // Allocation structure from API usually has 'ip' and 'port' or 'alias'
        // Just a helper to extract IP
        return $allocation['ip'] ?? '0.0.0.0';
    }
}
