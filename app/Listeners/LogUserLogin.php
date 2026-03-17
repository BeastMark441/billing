<?php

namespace App\Listeners;

use App\Models\UserLog;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AuditLogger $auditLogger) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        UserLog::create([
            'user_id' => $event->user->id,
            'action' => 'login',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'details' => 'User logged in',
        ]);

        $this->auditLogger->log('auth_login', ['ip' => Request::ip(), 'user_agent' => Request::userAgent()], 'user', (string) $event->user->id);

        Notification::send($event->user, new GeneralNotification(
            'Вход в систему',
            'Зафиксирован вход в аккаунт. IP: '.(Request::ip() ?? '-'),
            'info'
        ));
    }
}
