<?php

namespace App\Http\Controllers;

use App\Models\TelegramLinkToken;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramBotService $bot)
    {
        $secret = config('services.telegram.webhook_secret');
        if ($secret) {
            $header = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if (! is_string($header) || ! hash_equals($secret, $header)) {
                return response('Unauthorized', 401);
            }
        }

        $update = $request->all();
        $message = $update['message'] ?? null;
        if (! is_array($message)) {
            return response('OK', 200);
        }

        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;
        $telegramUserId = $message['from']['id'] ?? null;

        if (! is_string($text) || ! $chatId || ! $telegramUserId) {
            return response('OK', 200);
        }

        $trimmed = trim($text);
        if (Str::startsWith($trimmed, '/start')) {
            $parts = preg_split('/\s+/', $trimmed);
            $token = $parts[1] ?? null;
            if (! $token) {
                $bot->sendMessage((string) $chatId, 'Чтобы привязать аккаунт, откройте панель и сгенерируйте код привязки.');

                return response('OK', 200);
            }

            $link = TelegramLinkToken::where('token', $token)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->first();

            if (! $link) {
                $bot->sendMessage((string) $chatId, 'Код привязки недействителен или истек. Сгенерируйте новый код в панели.');

                return response('OK', 200);
            }

            $user = $link->user;
            $user->update([
                'telegram_user_id' => (string) $telegramUserId,
                'telegram_chat_id' => (string) $chatId,
                'telegram_linked_at' => now(),
            ]);

            $link->update(['used_at' => now()]);

            $bot->sendMessage((string) $chatId, 'Аккаунт успешно привязан. Уведомления можно настроить в панели или командой /notifications.');

            return response('OK', 200);
        }

        if (Str::startsWith($trimmed, '/notifications')) {
            $user = User::where('telegram_user_id', (string) $telegramUserId)->first();
            if (! $user) {
                $bot->sendMessage((string) $chatId, 'Сначала привяжите аккаунт через /start <код>.');

                return response('OK', 200);
            }

            $parts = preg_split('/\s+/', $trimmed);
            $arg = strtolower((string) ($parts[1] ?? ''));

            if ($arg === 'on') {
                $user->update(['notify_telegram' => true]);
                $bot->sendMessage((string) $chatId, 'Telegram-уведомления включены.');

                return response('OK', 200);
            }

            if ($arg === 'off') {
                $user->update(['notify_telegram' => false]);
                $bot->sendMessage((string) $chatId, 'Telegram-уведомления отключены.');

                return response('OK', 200);
            }

            $bot->sendMessage((string) $chatId, 'Текущий статус: '.(($user->notify_telegram ?? false) ? 'включено' : 'отключено').'. Используйте /notifications on или /notifications off.');

            return response('OK', 200);
        }

        return response('OK', 200);
    }
}
