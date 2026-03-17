<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserLog;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        UserLog::create([
            'user_id' => $request->user()->id,
            'action' => 'password_update',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => 'Password updated',
        ]);

        $this->auditLogger->log('auth_password_changed', ['ip' => $request->ip(), 'user_agent' => $request->userAgent()], 'user', (string) $request->user()->id, 'warning');

        $request->user()->notify(new GeneralNotification(
            'Смена пароля',
            'Пароль был изменен. Если это были не вы, срочно смените пароль и обратитесь в поддержку.',
            'warning'
        ));

        return back()->with('status', 'password-updated');
    }
}
