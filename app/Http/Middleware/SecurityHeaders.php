<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $isLocal = app()->environment('local');

        $scriptSrc = ["'self'", "'unsafe-inline'"];
        $styleSrc = ["'self'", "'unsafe-inline'"];
        $connectSrc = ["'self'", 'https:', 'wss:'];

        if ($isLocal) {
            $scriptSrc[] = "'unsafe-eval'";
            $scriptSrc[] = 'http://127.0.0.1:5173';
            $scriptSrc[] = 'http://localhost:5173';

            $styleSrc[] = 'http://127.0.0.1:5173';
            $styleSrc[] = 'http://localhost:5173';

            $connectSrc[] = 'http://127.0.0.1:5173';
            $connectSrc[] = 'http://localhost:5173';
            $connectSrc[] = 'ws://127.0.0.1:5173';
            $connectSrc[] = 'ws://localhost:5173';
        }

        $csp = implode('; ', array_filter([
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            'script-src '.implode(' ', array_values(array_unique($scriptSrc))),
            'style-src '.implode(' ', array_values(array_unique($styleSrc))),
            "img-src 'self' data: blob: https:",
            "font-src 'self' data:",
            'connect-src '.implode(' ', array_values(array_unique($connectSrc))),
        ]));

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
