<?php

namespace App\Notifications\Channels;

use App\Contracts\TelegramNotification;
use App\Notifications\TelegramMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $chatId = method_exists($notifiable, 'routeNotificationForTelegram')
            ? $notifiable->routeNotificationForTelegram()
            : null;

        if (! $chatId) {
            return;
        }

        if (! $notification instanceof TelegramNotification) {
            return;
        }

        $message = $notification->toTelegram($notifiable);
        if (! $message instanceof TelegramMessage) {
            return;
        }

        $token = config('services.telegram.bot_token');
        if (! $token) {
            return;
        }

        $response = Http::timeout(10)
            ->retry(2, 250)
            ->asJson()
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message->text,
                'disable_web_page_preview' => true,
            ]);

        if (! $response->successful() || ! ($response->json('ok') === true)) {
            Log::warning('Telegram sendMessage failed', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        }
    }
}
