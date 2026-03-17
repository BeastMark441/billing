<?php

namespace App\Listeners;

use App\Models\UserLog;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;

class LogPasswordReset
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AuditLogger $auditLogger) {}

    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        UserLog::create([
            'user_id' => $event->user->id,
            'action' => 'password_reset',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'details' => 'Password reset via email',
        ]);

        $this->auditLogger->log('auth_password_reset', ['ip' => Request::ip(), 'user_agent' => Request::userAgent()], 'user', (string) $event->user->id, 'warning');

        Notification::send($event->user, new GeneralNotification(
            'Сброс пароля',
            'Пароль был сброшен. Если это были не вы, срочно смените пароль и обратитесь в поддержку.',
            'warning',
            route('profile.edit'),
            'Открыть профиль'
        ));
    }
}
