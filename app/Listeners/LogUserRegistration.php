<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UserLog;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;

class LogUserRegistration
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AuditLogger $auditLogger) {}

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        UserLog::create([
            'user_id' => $event->user->id,
            'action' => 'register',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'details' => 'Account registered',
        ]);

        $this->auditLogger->log('auth_register', ['ip' => Request::ip(), 'user_agent' => Request::userAgent()], 'user', (string) $event->user->id);

        Notification::send($event->user, new GeneralNotification(
            'Регистрация',
            'Аккаунт успешно создан.',
            'success'
        ));

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::send($admin, new GeneralNotification(
                'Новый пользователь',
                'Зарегистрирован новый пользователь: '.$event->user->email.'.',
                'info',
                route('admin.users.index'),
                'Открыть пользователей'
            ));
        }
    }
}
