<?php

namespace App\Listeners;

use App\Models\UserLog;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Request;

class LogPasswordReset
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

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
    }
}
