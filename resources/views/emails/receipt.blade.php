<div style="font-family: Arial, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin: 0 0 12px;">Чек {{ $receipt->receipt_number }}</h2>
    <p style="margin: 0 0 8px;">Сумма: <strong>{{ number_format((float) $receipt->amount, 2, '.', ' ') }} ₽</strong></p>
    <p style="margin: 0 0 8px;">Дата: {{ $receipt->issued_at?->format('d.m.Y H:i') }}</p>
    <p style="margin: 16px 0 0;">PDF-чек во вложении. Также можно открыть по ссылке:</p>
    <p style="margin: 8px 0 0;"><a href="{{ $publicUrl }}">{{ $publicUrl }}</a></p>
</div>

