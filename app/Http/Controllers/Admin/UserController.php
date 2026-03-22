<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalanceLog;
use App\Models\BannedIp;
use App\Models\User;
use App\Models\UserLog;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'in:user,admin,superadmin'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Пользователь успешно создан.');
    }

    public function edit(User $user)
    {
        $balanceLogs = $user->balanceLogs()->latest()->paginate(5, ['*'], 'balance_page');
        $logs = $user->logs()->latest()->paginate(20, ['*'], 'logs_page');
        // Get last known IP from sessions if available
        $lastIp = DB::table('sessions')->where('user_id', $user->id)->orderBy('last_activity', 'desc')->value('ip_address');

        return view('admin.users.edit', compact('user', 'balanceLogs', 'lastIp', 'logs'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'surname' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'patronymic' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:user,admin'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'blocked_until' => ['nullable', 'date'],
            'blocked_reason' => ['nullable', 'string'],
        ]);

        $user->fill([
            'surname' => $validated['surname'],
            'name' => $validated['name'],
            'patronymic' => $validated['patronymic'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_blocked' => $request->has('is_blocked'),
            'blocked_until' => $validated['blocked_until'],
            'blocked_reason' => $validated['blocked_reason'],
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        UserLog::create([
            'user_id' => $user->id,
            'action' => 'admin_update',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => 'Updated by admin '.Auth::user()->name,
        ]);

        return redirect()->route('admin.users.edit', $user)->with('success', 'User updated successfully.');
    }

    public function updateBalance(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $amount = $validated['amount'];
        $type = $validated['type'];

        // Automatically handle signs based on type
        if (in_array($type, ['admin_deduction', 'penalty'])) {
            $amount = -abs($amount);
        } elseif (in_array($type, ['admin_deposit', 'bonus', 'refund'])) {
            $amount = abs($amount);
        }
        // 'correction' takes amount as is

        $user->balance += $amount;
        $user->save();

        BalanceLog::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type,
            'description' => $validated['description'],
            'admin_id' => Auth::id(),
        ]);

        UserLog::create([
            'user_id' => $user->id,
            'action' => 'admin_balance_update',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => 'Balance changed by '.$amount.' ('.$validated['type'].') by admin '.Auth::user()->name,
        ]);

        return back()->with('success', 'Balance updated successfully.');
    }

    public function verifyEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            $user->email_verified_at = null;
            $message = 'Email unverified.';
            $action = 'admin_unverify_email';
        } else {
            $user->markEmailAsVerified();
            $message = 'Email verified.';
            $action = 'admin_verify_email';
        }
        $user->save();

        UserLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $message.' by admin '.Auth::user()->name,
        ]);

        return back()->with('success', $message);
    }

    public function sendVerificationLink(User $user)
    {
        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return back()->with('success', 'Verification link sent.');
        }

        return back()->with('info', 'Email already verified.');
    }

    public function sendResetLink(User $user)
    {
        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('success', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function banIp(Request $request, User $user)
    {
        $ip = $request->input('ip');
        if (! $ip) {
            return back()->with('error', 'No IP provided.');
        }

        BannedIp::create([
            'ip_address' => $ip,
            'reason' => 'Banned from user '.$user->email,
        ]);

        UserLog::create([
            'user_id' => $user->id,
            'action' => 'admin_ban_ip',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => 'IP '.$ip.' banned by admin '.Auth::user()->name,
        ]);

        return back()->with('success', 'IP '.$ip.' banned successfully.');
    }

    public function notifyUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,danger,success'],
        ]);

        $user->notify(new GeneralNotification(
            'Уведомление от администратора',
            $validated['message'],
            $validated['type'] === 'danger' ? 'error' : $validated['type'] // map danger to error
        ));

        return back()->with('success', 'Уведомление отправлено.');
    }
}
