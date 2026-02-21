<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::orderBy('id', 'desc')->get();
    }

    public function show(User $user)
    {
        return $user->load(['servers', 'payments', 'tickets', 'loginLogs']);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'string|max:32',
            'email' => 'email|unique:users,email,' . $user->id,
            'balance' => 'numeric',
            'role' => 'in:user,admin',
            'is_blocked' => 'boolean',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'telegram' => 'nullable|string|max:32',
            'vk' => 'nullable|string|max:50',
        ]);

        $user->update($validated);
        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

    public function block(User $user)
    {
        $user->update(['is_blocked' => true]);
        return response()->json(['message' => 'User blocked']);
    }

    public function unblock(User $user)
    {
        $user->update(['is_blocked' => false]);
        return response()->json(['message' => 'User unblocked']);
    }

    public function verifyEmail(User $user)
    {
        $user->markEmailAsVerified();
        return response()->json(['message' => 'Email verified']);
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);
        return response()->json(['message' => 'Password reset successfully']);
    }
}
