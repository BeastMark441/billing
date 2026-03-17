<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptApiController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $query = Receipt::query();

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issued_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issued_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('amount_from')) {
            $query->where('amount', '>=', (float) $request->input('amount_from'));
        }
        if ($request->filled('amount_to')) {
            $query->where('amount', '<=', (float) $request->input('amount_to'));
        }
        if ($request->filled('type')) {
            $query->where('type', (string) $request->input('type'));
        }

        $receipts = $query->latest('issued_at')->paginate(30);

        return response()->json($receipts);
    }

    public function show(Request $request, Receipt $receipt)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        if (! $user->isAdmin() && (int) $receipt->user_id !== (int) $user->id) {
            abort(403);
        }

        return response()->json($receipt);
    }
}
