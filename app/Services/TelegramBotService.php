<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramBotService
{
    public function sendMessage(string $chatId, string $text): void
    {
        $token = config('services.telegram.bot_token');
        if (! $token) {
            return;
        }

        Http::timeout(10)
            ->retry(2, 250)
            ->asJson()
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'disable_web_page_preview' => true,
            ]);
    }
}
