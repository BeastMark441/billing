<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\BalanceLog::with('user')->latest();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            })->orWhere('description', 'like', "%{$search}%");
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $logs = $query->paginate(20);

        return view('admin.finance.index', compact('logs'));
    }
}
