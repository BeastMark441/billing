<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserLog;
use App\Notifications\GeneralNotification;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $beforeEmail = $user->email;

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();

            // Log email change
            UserLog::create([
                'user_id' => $user->id,
                'action' => 'email_update',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => 'Email updated',
            ]);

            $this->auditLogger->log('profile_email_changed', ['from' => $beforeEmail, 'to' => $user->email], 'user', (string) $user->id, 'warning');

            $user->notify(new GeneralNotification(
                'Смена Email',
                'Email был изменен. Если это были не вы, срочно смените пароль и обратитесь в поддержку.',
                'warning'
            ));
        }

        $user->save();

        $this->auditLogger->log('profile_updated', array_keys($request->validated()), 'user', (string) $user->id);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
