<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public function log(string $action, array $meta = [], ?string $objectType = null, ?string $objectId = null, string $severity = 'info'): void
    {
        $request = request();
        $correlationId = $request instanceof Request ? $request->attributes->get('correlation_id') : null;

        AuditLog::create([
            'user_id' => Auth::id(),
            'actor_role' => Auth::user()?->role,
            'action' => $action,
            'object_type' => $objectType,
            'object_id' => $objectId,
            'severity' => $severity,
            'correlation_id' => $correlationId,
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? $request->userAgent() : null,
            'meta' => $meta,
        ]);
    }
}
