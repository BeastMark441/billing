@extends('layouts.landing')

@section('title', 'Проверка чека '.$receipt->receipt_number.' — NODEUM')
@section('description', 'Онлайн-проверка подлинности чека '.$receipt->receipt_number.'.')

@section('content')
<section class="py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-[#050508] border border-white/10 rounded-2xl p-8">
            <h1 class="text-2xl font-bold text-white">Проверка подлинности</h1>
            <div class="mt-2 text-sm text-gray-400">Чек {{ $receipt->receipt_number }} от {{ $receipt->issued_at?->format('d.m.Y H:i') }}</div>

            @if($valid)
                <div class="mt-6 bg-green-500/10 border border-green-500/20 text-green-200 px-4 py-3 rounded-xl">Подлинность подтверждена.</div>
            @else
                <div class="mt-6 bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-xl">Подлинность не подтверждена.</div>
            @endif

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-gray-400">Сумма</div>
                    <div class="text-white font-semibold">{{ number_format((float) $receipt->amount, 2, '.', ' ') }} ₽</div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="text-gray-400">Способ оплаты</div>
                    <div class="text-white font-semibold">{{ $receipt->payment_method ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
