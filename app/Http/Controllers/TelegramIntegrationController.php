<?php

namespace App\Http\Controllers;

use App\Models\TelegramLinkToken;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TelegramIntegrationController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    public function startLink()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        TelegramLinkToken::where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        $token = Str::random(32);
        TelegramLinkToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->auditLogger->log('telegram_link_token_created');

        return redirect()->route('dashboard.security')->with('telegram_link_token', $token);
    }

    public function unlink()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'telegram_user_id' => null,
            'telegram_chat_id' => null,
            'telegram_linked_at' => null,
            'notify_telegram' => false,
        ]);

        $this->auditLogger->log('telegram_unlinked');

        return redirect()->route('dashboard.security')->with('success', 'Telegram отвязан.');
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'notify_email' => 'required|boolean',
            'notify_telegram' => 'required|boolean',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->telegram_chat_id) {
            $validated['notify_telegram'] = false;
        }

        $user->update($validated);
        $this->auditLogger->log('notification_preferences_updated', $validated);

        return redirect()->route('dashboard.security')->with('success', 'Настройки уведомлений сохранены.');
    }
}
