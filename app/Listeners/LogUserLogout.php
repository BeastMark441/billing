<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogUserLogout
{
    public function __construct(protected AuditLogger $auditLogger) {}

    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        $userId = method_exists($event->user, 'getAuthIdentifier')
            ? (string) $event->user->getAuthIdentifier()
            : null;

        if (! $userId) {
            return;
        }

        $this->auditLogger->log('auth_logout', ['ip' => Request::ip(), 'user_agent' => Request::userAgent()], 'user', $userId);
    }
}
