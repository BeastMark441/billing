<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\InfrastructureCategory;
use App\Models\TelegramLinkToken;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $recentActivity = AuditLog::where('user_id', $user->id)
            ->where('action', 'not like', 'admin_%')
            ->latest()
            ->take(10)
            ->get();

        $recentOrders = $user->orders()->with('service')->latest()->take(5)->get();

        return view('dashboard.overview', [
            'user' => $user,
            'recentActivity' => $recentActivity,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Display the account information page.
     */
    public function account()
    {
        return view('dashboard.account', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Display the security settings page.
     */
    public function security()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $logs = AuditLog::where('user_id', $user->id)
            ->where('action', 'not like', 'admin_%')
            ->latest()
            ->take(5)
            ->get();

        $token = session('telegram_link_token');
        if (! $token) {
            $token = TelegramLinkToken::where('user_id', $user->id)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->latest()
                ->value('token');
        }

        $botUsername = (string) config('services.telegram.bot_username');
        $deepLink = $botUsername && $token ? "https://t.me/{$botUsername}?start={$token}" : null;

        return view('dashboard.security', [
            'user' => $user,
            'logs' => $logs,
            'telegramLinkToken' => $token,
            'telegramDeepLink' => $deepLink,
            'telegramBotUsername' => $botUsername,
        ]);
    }

    /**
     * Display user logs.
     */
    public function logs()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $logs = AuditLog::where('user_id', $user->id)
            ->where('action', 'not like', 'admin_%')
            ->latest()
            ->paginate(20);

        return view('dashboard.logs', compact('logs'));
    }

    /**
     * Display the infrastructure page.
     */
    public function infrastructure()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $categories = InfrastructureCategory::where('is_active', true)
            ->with(['subcategories' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->with(['services' => function ($q) {
                        $q->where('is_active', true)
                            ->orderBy('sort_order')
                            ->orderBy('name');
                    }]);
            }, 'services' => function ($query) {
                $query->where('infrastructure_subcategory_id', null)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Только активные услуги пользователя (без корзины)
        $orders = $user->orders()
            ->whereIn('status', ['paid', 'active', 'pending', 'suspended', 'provisioning', 'failed'])
            ->with('service')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.infrastructure', compact('categories', 'orders'));
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationAsRead($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }
}
