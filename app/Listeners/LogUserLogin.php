<?php

namespace App\Listeners;

use App\Models\UserLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogUserLogin
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
    public function handle(Login $event): void
    {
        UserLog::create([
            'user_id' => $event->user->id,
            'action' => 'login',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'details' => 'User logged in',
        ]);
    }
}
