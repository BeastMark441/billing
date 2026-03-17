<?php

namespace App\Services;

use App\Mail\ReceiptMail;
use App\Models\Receipt;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReceiptService
{
    public function __construct(protected AuditLogger $auditLogger) {}

    public function issueForDeposit(User $user, float $amount, array $context = []): Receipt
    {
        return $this->issue(
            user: $user,
            type: 'deposit',
            amount: $amount,
            paymentMethod: $context['payment_method'] ?? 'T-Bank',
            items: [
                [
                    'name' => 'Пополнение баланса',
                    'quantity' => 1,
                    'price' => $amount,
                    'sum' => $amount,
                ],
            ],
            relatedType: $context['related_type'] ?? 'payment',
            relatedId: $context['related_id'] ?? null,
            meta: $context,
        );
    }

    public function issueForPurchase(User $user, string $serviceName, float $amount, array $context = []): Receipt
    {
        return $this->issue(
            user: $user,
            type: 'purchase',
            amount: $amount,
            paymentMethod: $context['payment_method'] ?? 'Баланс',
            items: [
                [
                    'name' => $serviceName,
                    'quantity' => 1,
                    'price' => $amount,
                    'sum' => $amount,
                ],
            ],
            relatedType: $context['related_type'] ?? 'order',
            relatedId: $context['related_id'] ?? null,
            meta: $context,
        );
    }

    public function verifyPublic(Receipt $receipt, string $token): bool
    {
        if (! hash_equals((string) $receipt->public_token, (string) $token)) {
            return false;
        }

        $payload = $this->signaturePayload($receipt);

        return hash_equals(
            (string) $receipt->signature,
            hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), (string) config('app.key'))
        );
    }

    protected function issue(
        User $user,
        string $type,
        float $amount,
        string $paymentMethod,
        array $items,
        ?string $relatedType = null,
        ?string $relatedId = null,
        array $meta = [],
    ): Receipt {
        $seller = [
            'name' => (string) env('ORG_NAME', config('app.name', 'NODEUM')),
            'inn' => (string) env('ORG_INN', ''),
            'kpp' => (string) env('ORG_KPP', ''),
            'address' => (string) env('ORG_ADDRESS', ''),
            'phone' => (string) env('ORG_PHONE', ''),
            'email' => (string) env('ORG_EMAIL', ''),
            'site' => (string) env('ORG_SITE', (string) config('app.url')),
        ];

        $buyer = [
            'email' => (string) $user->email,
            'name' => (string) $user->name,
            'user_id' => (string) $user->id,
        ];

        $issuedAt = now();
        $receiptNumber = 'RCPT-'.$issuedAt->format('Ymd').'-'.strtoupper(Str::random(8));
        $publicToken = Str::random(48);

        $receipt = new Receipt([
            'user_id' => $user->id,
            'receipt_number' => $receiptNumber,
            'type' => $type,
            'amount' => $amount,
            'currency' => 'RUB',
            'payment_method' => $paymentMethod,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'seller' => $seller,
            'buyer' => $buyer,
            'items' => $items,
            'meta' => $meta,
            'public_token' => $publicToken,
            'signature' => '0',
            'issued_at' => $issuedAt,
        ]);
        $receipt->save();

        $payload = $this->signaturePayload($receipt);
        $signature = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), (string) config('app.key'));
        $receipt->update(['signature' => $signature]);

        $this->generatePdf($receipt);
        $this->sendEmail($receipt);

        $this->auditLogger->log('receipt_issued', [
            'receipt_id' => $receipt->id,
            'receipt_number' => $receipt->receipt_number,
            'type' => $receipt->type,
            'amount' => (float) $receipt->amount,
        ], 'receipt', (string) $receipt->id);

        return $receipt->fresh();
    }

    protected function signaturePayload(Receipt $receipt): array
    {
        return [
            'id' => (string) $receipt->id,
            'receipt_number' => (string) $receipt->receipt_number,
            'type' => (string) $receipt->type,
            'amount' => (string) $receipt->amount,
            'currency' => (string) $receipt->currency,
            'payment_method' => (string) $receipt->payment_method,
            'issued_at' => $receipt->issued_at?->toIso8601String(),
            'seller' => $receipt->seller,
            'buyer' => $receipt->buyer,
            'items' => $receipt->items,
        ];
    }

    protected function generatePdf(Receipt $receipt): void
    {
        $verifyUrl = route('receipts.public.verify', ['receipt' => $receipt->id, 'token' => $receipt->public_token]);

        $qr = new QrCode(
            data: $verifyUrl,
            size: 220,
            margin: 0,
        );
        $writer = new PngWriter;
        $qrDataUri = $writer->write($qr)->getDataUri();

        $html = view('receipts.pdf', [
            'receipt' => $receipt,
            'verifyUrl' => $verifyUrl,
            'qrDataUri' => $qrDataUri,
        ])->render();

        $options = new Options;
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsRemoteEnabled(false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdf = $dompdf->output();

        $path = 'receipts/'.$receipt->id.'.pdf';
        Storage::disk('local')->put($path, $pdf);

        $receipt->update([
            'pdf_path' => $path,
            'pdf_sha256' => hash('sha256', $pdf),
        ]);
    }

    protected function sendEmail(Receipt $receipt): void
    {
        $email = $receipt->buyer['email'] ?? null;
        if (! $email) {
            return;
        }

        if (! $receipt->pdf_path) {
            return;
        }

        $pdf = Storage::disk('local')->get($receipt->pdf_path);
        Mail::to((string) $email)->send(new ReceiptMail($receipt, $pdf));

        $this->auditLogger->log('receipt_emailed', [
            'receipt_id' => $receipt->id,
            'email' => (string) $email,
        ], 'receipt', (string) $receipt->id);
    }
}
