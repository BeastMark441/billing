<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Services\AuditLogger;
use App\Services\ReceiptService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicReceiptController extends Controller
{
    public function __construct(protected ReceiptService $receiptService, protected AuditLogger $auditLogger) {}

    public function show(Receipt $receipt, string $token): View
    {
        abort_unless($this->receiptService->verifyPublic($receipt, $token), 404);

        return view('public.receipt', compact('receipt', 'token'));
    }

    public function download(Receipt $receipt, string $token): StreamedResponse
    {
        abort_unless($this->receiptService->verifyPublic($receipt, $token), 404);
        abort_unless($receipt->pdf_path && Storage::disk('local')->exists($receipt->pdf_path), 404);

        $this->auditLogger->log('receipt_downloaded_public', ['receipt_id' => $receipt->id], 'receipt', (string) $receipt->id);

        return Storage::disk('local')->download($receipt->pdf_path, $receipt->receipt_number.'.pdf');
    }

    public function verify(Receipt $receipt, string $token): View
    {
        $valid = $this->receiptService->verifyPublic($receipt, $token);
        $this->auditLogger->log('receipt_verified', ['receipt_id' => $receipt->id, 'valid' => $valid], 'receipt', (string) $receipt->id);

        return view('public.receipt-verify', compact('receipt', 'valid'));
    }
}
