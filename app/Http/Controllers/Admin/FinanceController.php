<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalanceLog;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $query = BalanceLog::with('user')->latest();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($inner) use ($search) {
                $inner->whereHas('user', function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('amount_from')) {
            $query->where('amount', '>=', (float) $request->input('amount_from'));
        }

        if ($request->filled('amount_to')) {
            $query->where('amount', '<=', (float) $request->input('amount_to'));
        }

        $total = (clone $query)->count();

        $logs = $query->paginate(20);

        return view('admin.finance.index', compact('logs', 'total'));
    }

    public function destroy(Request $request, BalanceLog $log): RedirectResponse
    {
        $request->validate([
            'confirm' => ['required', 'in:DELETE'],
            'password' => ['required', 'current_password'],
        ]);

        $deletedId = $log->id;
        $targetUserId = $log->user_id;
        $amount = (float) $log->amount;
        $type = (string) $log->type;

        $log->delete();

        $this->auditLogger->log('admin_finance_log_deleted', [
            'log_id' => $deletedId,
            'target_user_id' => $targetUserId,
            'amount' => $amount,
            'type' => $type,
        ], 'balance_log', (string) $deletedId, 'warning');

        return redirect()->back()->with('success', 'Запись финансовой истории удалена.');
    }

    public function destroyFiltered(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'confirm' => ['required', 'in:DELETE FINANCE'],
            'password' => ['required', 'current_password'],
            'search' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'amount_from' => ['nullable', 'numeric'],
            'amount_to' => ['nullable', 'numeric'],
        ]);

        $query = BalanceLog::query();

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($inner) use ($search) {
                $inner->whereHas('user', function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (! empty($validated['user_id'])) {
            $query->where('user_id', (int) $validated['user_id']);
        }

        if (! empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (! empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        if (! empty($validated['amount_from'])) {
            $query->where('amount', '>=', (float) $validated['amount_from']);
        }

        if (! empty($validated['amount_to'])) {
            $query->where('amount', '<=', (float) $validated['amount_to']);
        }

        $count = (clone $query)->count();
        $query->delete();

        $this->auditLogger->log('admin_finance_logs_deleted', [
            'deleted' => $count,
            'filters' => [
                'search' => $validated['search'] ?? null,
                'type' => $validated['type'] ?? null,
                'user_id' => $validated['user_id'] ?? null,
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
                'amount_from' => $validated['amount_from'] ?? null,
                'amount_to' => $validated['amount_to'] ?? null,
            ],
        ], null, null, 'warning');

        return redirect()->route('admin.finance.index')->with('success', 'Удалено записей: '.$count);
    }
}
