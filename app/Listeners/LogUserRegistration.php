<?php

namespace App\Listeners;

use App\Models\UserLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Request;

class LogUserRegistration
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
    public function handle(Registered $event): void
    {
        UserLog::create([
            'user_id' => $event->user->id,
            'action' => 'register',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'details' => 'Account registered',
        ]);
    }
}
