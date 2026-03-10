<?php

namespace App\Http\Middleware;

use App\Models\BannedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check banned IP
        $bannedIp = BannedIp::where('ip_address', $request->ip())
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($bannedIp) {
            abort(403, 'Your IP address is banned. Reason: '.$bannedIp->reason);
        }

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->is_blocked) {
                // Check blocked_until
                if ($user->blocked_until && $user->blocked_until->isPast()) {
                    // Auto unblock if expired
                    $user->update(['is_blocked' => false, 'blocked_until' => null, 'blocked_reason' => null]);
                } else {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->withErrors(['email' => 'Your account is blocked. Reason: '.$user->blocked_reason]);
                }
            }
        }

        return $next($request);
    }
}
