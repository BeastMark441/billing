<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

uses(RefreshDatabase::class);

it('returns 422 when TBank credentials are missing', function () {
    Config::set('services.tbank.terminal_key', null);
    Config::set('services.tbank.password', null);
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $res = $this->postJson('/api/client/balance/topup', ['amount' => 100]);
    $res->assertStatus(422)->assertJsonStructure(['error']);
});

