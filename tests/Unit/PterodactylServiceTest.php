<?php

namespace Tests\Unit;

use App\Services\PterodactylService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PterodactylServiceTest extends TestCase
{
    public function test_find_user_by_email_returns_attributes()
    {
        Config::set('services.pterodactyl.url', 'https://panel.example.com');
        Config::set('services.pterodactyl.key', 'app_key');
        Config::set('services.pterodactyl.client_key', 'client_key');
        Config::set('services.pterodactyl.verify', true);
        Config::set('services.pterodactyl.is_pelican', false);

        Http::fake([
            'https://panel.example.com/api/application/users*' => Http::response([
                'data' => [
                    ['attributes' => ['id' => 42, 'email' => 'foo@example.com']]
                ]
            ], 200),
        ]);

        $svc = new PterodactylService();
        $attrs = $svc->findUserByEmail('foo@example.com');

        $this->assertNotNull($attrs);
        $this->assertSame(42, $attrs['id']);
    }

    public function test_create_server_drops_nest_when_pelican_enabled()
    {
        Config::set('services.pterodactyl.url', 'https://panel.example.com');
        Config::set('services.pterodactyl.key', 'app_key');
        Config::set('services.pterodactyl.client_key', 'client_key');
        Config::set('services.pterodactyl.verify', true);
        Config::set('services.pterodactyl.is_pelican', true);

        $capturedBody = null;

        Http::fake([
            'https://panel.example.com/api/application/servers' => function ($request) use (&$capturedBody) {
                $capturedBody = $request->data();
                return Http::response(['attributes' => ['id' => 1, 'identifier' => 'abcd', 'name' => 'srv']], 200);
            },
        ]);

        $svc = new PterodactylService();
        $svc->createServer([
            'name' => 'srv',
            'user' => 1,
            'nest' => 99,
            'docker_image' => 'image:tag',
            'limits' => ['memory' => 512, 'disk' => 1024, 'cpu' => 50, 'io' => 500, 'swap' => 0],
            'feature_limits' => ['databases' => 0, 'allocations' => 0, 'backups' => 0],
            'allocation' => ['default' => 123],
        ]);

        $this->assertIsArray($capturedBody);
        $this->assertArrayNotHasKey('nest', $capturedBody);
        $this->assertSame('srv', $capturedBody['name']);
    }
}

