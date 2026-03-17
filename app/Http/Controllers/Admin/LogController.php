<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\UserLog;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $source = $request->input('source', 'audit');

        $filters = [
            'source' => $source,
            'user_id' => $request->input('user_id'),
            'action' => $request->input('action'),
            'severity' => $request->input('severity'),
            'search' => $request->input('search'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        if ($source === 'user') {
            $query = UserLog::query()->latest();

            if ($filters['user_id']) {
                $query->where('user_id', (int) $filters['user_id']);
            }
            if ($filters['action']) {
                $query->where('action', 'like', '%'.$filters['action'].'%');
            }
            if ($filters['search']) {
                $q = (string) $filters['search'];
                $query->where(function ($inner) use ($q) {
                    $inner->where('details', 'like', '%'.$q.'%')
                        ->orWhere('ip_address', 'like', '%'.$q.'%')
                        ->orWhere('user_agent', 'like', '%'.$q.'%');
                });
            }
            if ($filters['date_from']) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }
            if ($filters['date_to']) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $total = (clone $query)->count();
            $logs = $query->paginate(30)->withQueryString();

            return view('admin.logs.index', compact('logs', 'filters', 'total'));
        }

        $query = AuditLog::query()->latest();

        if ($filters['user_id']) {
            $query->where('user_id', (int) $filters['user_id']);
        }
        if ($filters['action']) {
            $query->where('action', 'like', '%'.$filters['action'].'%');
        }
        if ($filters['severity']) {
            $query->where('severity', $filters['severity']);
        }
        if ($filters['search']) {
            $q = (string) $filters['search'];
            $query->where(function ($inner) use ($q) {
                $inner->where('correlation_id', 'like', '%'.$q.'%')
                    ->orWhere('object_id', 'like', '%'.$q.'%')
                    ->orWhere('object_type', 'like', '%'.$q.'%')
                    ->orWhere('ip_address', 'like', '%'.$q.'%')
                    ->orWhere('user_agent', 'like', '%'.$q.'%');
            });
        }
        if ($filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $total = (clone $query)->count();
        $logs = $query->paginate(30)->withQueryString();

        return view('admin.logs.index', compact('logs', 'filters', 'total'));
    }

    public function destroyUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source' => ['required', 'in:audit,user'],
            'user_id' => ['required', 'integer', 'min:1'],
            'action' => ['nullable', 'string'],
            'severity' => ['nullable', 'string'],
            'search' => ['nullable', 'string'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'confirm' => ['required', 'in:DELETE'],
            'password' => ['required', 'current_password'],
        ]);

        $source = $validated['source'];
        $userId = (int) $validated['user_id'];

        $query = $source === 'user'
            ? UserLog::query()->where('user_id', $userId)
            : AuditLog::query()->where('user_id', $userId);

        if (! empty($validated['action'])) {
            $query->where('action', 'like', '%'.$validated['action'].'%');
        }
        if ($source === 'audit' && ! empty($validated['severity'])) {
            $query->where('severity', $validated['severity']);
        }
        if (! empty($validated['search'])) {
            $q = (string) $validated['search'];
            $query->where(function ($inner) use ($q, $source) {
                if ($source === 'user') {
                    $inner->where('details', 'like', '%'.$q.'%')
                        ->orWhere('ip_address', 'like', '%'.$q.'%')
                        ->orWhere('user_agent', 'like', '%'.$q.'%');

                    return;
                }

                $inner->where('correlation_id', 'like', '%'.$q.'%')
                    ->orWhere('object_id', 'like', '%'.$q.'%')
                    ->orWhere('object_type', 'like', '%'.$q.'%')
                    ->orWhere('ip_address', 'like', '%'.$q.'%')
                    ->orWhere('user_agent', 'like', '%'.$q.'%');
            });
        }
        if (! empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }
        if (! empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $count = (clone $query)->count();
        $query->delete();

        $this->auditLogger->log('admin_logs_user_deleted', [
            'source' => $source,
            'target_user_id' => $userId,
            'deleted' => $count,
            'filters' => [
                'action' => $validated['action'] ?? null,
                'severity' => $validated['severity'] ?? null,
                'search' => $validated['search'] ?? null,
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
            ],
        ], 'user', (string) $userId, 'warning');

        return redirect()->route('admin.logs.index', [
            'source' => $source,
        ])->with('success', 'Удалено записей: '.$count);
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'confirm' => ['required', 'in:DELETE ALL'],
            'password' => ['required', 'current_password'],
        ]);

        $auditCount = AuditLog::count();
        $userCount = UserLog::count();

        AuditLog::query()->delete();
        UserLog::query()->delete();

        $this->auditLogger->log('admin_logs_all_deleted', [
            'audit_deleted' => $auditCount,
            'user_deleted' => $userCount,
        ], null, null, 'warning');

        return redirect()->route('admin.logs.index')->with('success', 'Логи очищены. Audit: '.$auditCount.', User: '.$userCount);
    }
}
