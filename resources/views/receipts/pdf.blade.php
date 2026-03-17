<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; }
        .h1 { font-size: 18px; font-weight: 700; margin: 0; }
        .muted { color: #555; }
        .block { margin-top: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        th { background: #f5f5f5; font-weight: 700; }
        .right { text-align: right; }
        .qr { width: 140px; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <p class="h1">Чек {{ $receipt->receipt_number }}</p>
            <p class="muted" style="margin: 4px 0 0;">Дата и время: {{ $receipt->issued_at?->format('d.m.Y H:i') }}</p>
            <p class="muted" style="margin: 2px 0 0;">Способ оплаты: {{ $receipt->payment_method ?? '—' }}</p>
        </div>
        <div class="qr">
            <img src="{{ $qrDataUri }}" style="width: 140px; height: 140px;" alt="QR" />
        </div>
    </div>

    <div class="block">
        <table>
            <tr>
                <th style="width: 50%;">Продавец</th>
                <th style="width: 50%;">Покупатель</th>
            </tr>
            <tr>
                <td>
                    <div><strong>{{ $receipt->seller['name'] ?? '' }}</strong></div>
                    @if(!empty($receipt->seller['inn']))<div>ИНН: {{ $receipt->seller['inn'] }}</div>@endif
                    @if(!empty($receipt->seller['kpp']))<div>КПП: {{ $receipt->seller['kpp'] }}</div>@endif
                    @if(!empty($receipt->seller['address']))<div>Адрес: {{ $receipt->seller['address'] }}</div>@endif
                    @if(!empty($receipt->seller['phone']))<div>Телефон: {{ $receipt->seller['phone'] }}</div>@endif
                    @if(!empty($receipt->seller['email']))<div>Email: {{ $receipt->seller['email'] }}</div>@endif
                    @if(!empty($receipt->seller['site']))<div>Сайт: {{ $receipt->seller['site'] }}</div>@endif
                </td>
                <td>
                    <div><strong>{{ $receipt->buyer['name'] ?? '' }}</strong></div>
                    <div>Email: {{ $receipt->buyer['email'] ?? '' }}</div>
                    <div class="muted">User ID: {{ $receipt->buyer['user_id'] ?? '' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="block">
        <table>
            <thead>
                <tr>
                    <th>Товар/услуга</th>
                    <th class="right" style="width: 80px;">Кол-во</th>
                    <th class="right" style="width: 110px;">Цена</th>
                    <th class="right" style="width: 120px;">Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($receipt->items ?? []) as $item)
                    <tr>
                        <td>{{ $item['name'] ?? '' }}</td>
                        <td class="right">{{ $item['quantity'] ?? 1 }}</td>
                        <td class="right">{{ number_format((float) ($item['price'] ?? 0), 2, '.', ' ') }} ₽</td>
                        <td class="right">{{ number_format((float) ($item['sum'] ?? 0), 2, '.', ' ') }} ₽</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="right">Итого</th>
                    <th class="right">{{ number_format((float) $receipt->amount, 2, '.', ' ') }} ₽</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="block small">
        <div>Проверка подлинности: {{ $verifyUrl }}</div>
        <div class="muted">Защита: HMAC-SHA256 подпись документа встроена в запись системы.</div>
    </div>
</body>
</html>

