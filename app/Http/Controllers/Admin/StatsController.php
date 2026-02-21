<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'users_count' => User::count(),
            'active_servers' => Server::where('status', 'active')->count(),
            'monthly_revenue' => Payment::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            // Placeholder for node load, would need Ptero API calls or local metrics
            'node_load' => 0, 
        ]);
    }
}
