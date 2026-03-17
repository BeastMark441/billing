<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! Auth::check() || ! $user?->isSuperAdmin()) {
            abort(403, 'Требуются права супер-администратора.');
        }

        return $next($request);
    }
}
