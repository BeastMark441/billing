<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_mass_assignment_protection()
    {
        // Attempt to create user with role=admin via mass assignment
        $user = User::create([
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password',
            'role' => 'admin',
            'balance' => 999999,
        ]);

        $user->refresh(); // Refresh to get DB defaults since we didn't set role/balance

        // Should be created but with default role (user) and balance (0)
        // Note: 'role' default is likely 'user' from migration
        $this->assertEquals('user', $user->role);
        $this->assertEquals(0, $user->balance);

        // Attempt to update via mass assignment
        $user->update([
            'role' => 'admin',
            'balance' => 1000,
        ]);

        $user->refresh();
        $this->assertEquals('user', $user->role);
        $this->assertEquals(0, $user->balance);
    }

    public function test_security_headers_are_present()
    {
        $response = $this->get('/api/products');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_rate_limiting_headers()
    {
        // We need to hit a route that has throttle
        // /api/products has 'throttle:api'
        $response = $this->get('/api/products');
        
        // Laravel throttle middleware adds X-RateLimit-Limit and X-RateLimit-Remaining headers
        // But only if we are using the 'throttle' middleware.
        // Let's check if headers exist.
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }
}
