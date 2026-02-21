<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class PterodactylService
{
    protected $url;
    protected $apiKey;
    protected $clientApiKey;
    protected $verify;
    protected $caPath;
    protected $isPelican;

    public function __construct()
    {
        $this->url = config('services.pterodactyl.url');
        $this->apiKey = config('services.pterodactyl.key'); // Application API Key
        $this->clientApiKey = config('services.pterodactyl.client_key'); // Client API Key (Usually generated for admin)
        $this->verify = config('services.pterodactyl.verify', true);
        $this->caPath = config('services.pterodactyl.ca'); // path to cacert.pem if provided
        $this->isPelican = (bool) (config('services.pterodactyl.is_pelican', false));
    }

    protected function client()
    {
        $verify = $this->caPath ?: $this->verify;
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => $verify
        ])->baseUrl($this->url);
    }

    // New method for Client API interactions
    protected function clientApi()
    {
        // Note: Ideally, we should use the user's specific API key or an admin client key.
        // For this MVP, we assume we have a master Client API key in env.
        $verify = $this->caPath ?: $this->verify;
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->clientApiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => $verify
        ])->baseUrl($this->url);
    }

    public function createUser($data)
    {
        // $data: email, username, first_name, last_name
        $response = $this->client()->post('/api/application/users', $data);
        if ($response->failed()) {
            throw new Exception('Pterodactyl User Creation Failed: ' . $response->body());
        }
        return $response->json()['attributes'];
    }

    public function isPelican(): bool
    {
        return $this->isPelican;
    }

    public function findUserByEmail(string $email): ?array
    {
        $response = $this->client()->get('/api/application/users', [
            'filter' => ['email' => $email],
            'include' => '',
            'per_page' => 1,
        ]);
        if ($response->failed()) {
            return null;
        }
        $data = $response->json()['data'] ?? [];
        if (count($data) === 0) {
            return null;
        }
        return $data[0]['attributes'] ?? null;
    }

    public function createServer($data)
    {
        if ($this->isPelican && isset($data['nest'])) {
            unset($data['nest']);
        }
        $response = $this->client()->post('/api/application/servers', $data);
        if ($response->failed()) {
            throw new Exception('Pterodactyl Server Creation Failed: ' . $response->body());
        }
        return $response->json()['attributes'];
    }

    public function suspendServer($serverId)
    {
        $response = $this->client()->post("/api/application/servers/{$serverId}/suspend");
        if ($response->failed()) {
            throw new Exception('Pterodactyl Server Suspend Failed: ' . $response->body());
        }
        return true;
    }

    public function unsuspendServer($serverId)
    {
        $response = $this->client()->post("/api/application/servers/{$serverId}/unsuspend");
        if ($response->failed()) {
            throw new Exception('Pterodactyl Server Unsuspend Failed: ' . $response->body());
        }
        return true;
    }

    public function deleteServer($serverId)
    {
        $response = $this->client()->delete("/api/application/servers/{$serverId}");
        if ($response->failed()) {
            throw new Exception('Pterodactyl Server Deletion Failed: ' . $response->body());
        }
        return true;
    }
    
    public function getNodes()
    {
        $response = $this->client()->get('/api/application/nodes');
        if ($response->failed()) {
             throw new Exception('Pterodactyl Nodes Fetch Failed: ' . $response->body());
        }
        return $response->json()['data'];
    }

    public function getAllocations($nodeId)
    {
        $response = $this->client()->get("/api/application/nodes/{$nodeId}/allocations");
         if ($response->failed()) {
             throw new Exception('Pterodactyl Allocations Fetch Failed: ' . $response->body());
        }
        return $response->json()['data'];
    }

    // --- Client API Methods for Stats/Power ---

    public function getServerResources($pteroIdentifier)
    {
        // Requires Client API Key
        $response = $this->clientApi()->get("/api/client/servers/{$pteroIdentifier}/resources");
        if ($response->failed()) {
            // It might fail if server is installing/suspended
            return null; 
        }
        return $response->json()['attributes'];
    }

    public function sendPowerAction($pteroIdentifier, $signal)
    {
        // signal: start, stop, restart, kill
        $response = $this->clientApi()->post("/api/client/servers/{$pteroIdentifier}/power", [
            'signal' => $signal
        ]);
        
        if ($response->failed()) {
            throw new Exception('Power Action Failed: ' . $response->body());
        }
        return true;
    }
    
    public function getServerDetails($serverId)
    {
         // Get Application Server Details to find identifier if needed, or use existing ptero_server_id
         // But for Client API we need the "identifier" (short UUID), not the numeric ID.
         // We should probably store "identifier" in our DB or fetch it.
         $response = $this->client()->get("/api/application/servers/{$serverId}");
         if ($response->failed()) {
             throw new Exception('Server Fetch Failed: ' . $response->body());
         }
         return $response->json()['attributes'];
    }
}
