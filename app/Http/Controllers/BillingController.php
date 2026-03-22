<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->balanceLogs()->orderBy('created_at', 'desc');

        $pendingPaymentsQuery = $user->payments()
            ->whereNull('credited_at')
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->orderBy('created_at', 'desc');

        // Default to current and previous month if no date filter is applied
        if (! $request->has('date_from') && ! $request->has('date_to')) {
            $start = Carbon::now()->subMonth()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $query->whereBetween('created_at', [$start, $end]);
            $pendingPaymentsQuery->whereBetween('created_at', [$start, $end]);
        } else {
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
                $pendingPaymentsQuery->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
                $pendingPaymentsQuery->whereDate('created_at', '<=', $request->date_to);
            }
        }

        $transactions = $query->paginate(10);
        $pendingPayments = $pendingPaymentsQuery->get();

        return view('dashboard.billing.overview', [
            'user' => $user,
            'transactions' => $transactions,
            'pendingPayments' => $pendingPayments,
        ]);
    }

    public function expenses(Request $request)
    {
        // Redirect to overview as expenses are now there
        return redirect()->route('dashboard.billing');
    }
}
