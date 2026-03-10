<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureCategory;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
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
        $user = Auth::user();
        $logs = $user->logs()
            ->where('action', 'not like', 'admin_%')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.security', [
            'user' => $user,
            'logs' => $logs,
        ]);
    }

    /**
     * Display user logs.
     */
    public function logs()
    {
        $logs = Auth::user()->logs()
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

        $orders = $user->orders()->with('service')->latest()->take(5)->get();

        return view('dashboard.infrastructure', compact('categories', 'orders'));
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return back();
    }
}
