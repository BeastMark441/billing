<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptController extends Controller
{
    public function __construct(protected AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Receipt::query()->where('user_id', $user->id)->latest('issued_at');

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

        $receipts = $query->paginate(20)->withQueryString();

        return view('dashboard.receipts.index', compact('receipts', 'user'));
    }

    public function show(Receipt $receipt): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless((int) $receipt->user_id === (int) $user->id, 403);

        return view('dashboard.receipts.show', compact('receipt'));
    }

    public function download(Receipt $receipt): StreamedResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless((int) $receipt->user_id === (int) $user->id || $user->isAdmin(), 403);

        abort_unless($receipt->pdf_path && Storage::disk('local')->exists($receipt->pdf_path), 404);

        $this->auditLogger->log('receipt_downloaded', ['receipt_id' => $receipt->id], 'receipt', (string) $receipt->id);

        $filename = $receipt->receipt_number.'.pdf';

        return Storage::disk('local')->download($receipt->pdf_path, $filename);
    }
}
