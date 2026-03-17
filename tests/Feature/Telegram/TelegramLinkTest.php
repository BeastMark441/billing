<?php

namespace Tests\Feature\Telegram;

use App\Models\TelegramLinkToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class TelegramLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_telegram_start_links_user(): void
    {
        config([
            'services.telegram.bot_token' => 'test-token',
        ]);

        Http::fake();

        $user = User::factory()->create();
        $token = Str::random(32);

        TelegramLinkToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(15),
        ]);

        $update = [
            'update_id' => 1,
            'message' => [
                'message_id' => 1,
                'from' => ['id' => 12345],
                'chat' => ['id' => 67890],
                'text' => '/start '.$token,
            ],
        ];

        $this->postJson('/telegram/webhook', $update)->assertOk();

        $user->refresh();
        $this->assertSame('12345', $user->telegram_user_id);
        $this->assertSame('67890', $user->telegram_chat_id);
        $this->assertNotNull($user->telegram_linked_at);
        $this->assertDatabaseHas('telegram_link_tokens', ['token' => $token]);
        $this->assertNotNull(TelegramLinkToken::where('token', $token)->value('used_at'));
    }
}
