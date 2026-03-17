<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->headers->get('X-Request-Id')
            ?? $request->headers->get('X-Correlation-Id')
            ?? (string) Str::uuid();

        $request->attributes->set('correlation_id', $correlationId);

        $context = [
            'correlation_id' => $correlationId,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => optional($request->route())->getName(),
        ];

        if (Auth::check()) {
            $context['user_id'] = Auth::id();
            $context['user_role'] = Auth::user()?->role;
        }

        Log::withContext($context);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $correlationId);

        return $response;
    }
}
