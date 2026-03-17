@extends('layouts.landing')

@section('title', 'Чек '.$receipt->receipt_number.' — NODEUM')
@section('description', 'Чек '.$receipt->receipt_number.' для онлайн-проверки и скачивания.')

@section('content')
<section class="py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-[#050508] border border-white/10 rounded-2xl p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white">Чек {{ $receipt->receipt_number }}</h1>
                    <div class="text-sm text-gray-400 mt-1">{{ $receipt->issued_at?->format('d.m.Y H:i') }} · {{ $receipt->payment_method ?? '—' }}</div>
                </div>
                <a href="{{ route('receipts.public.download', ['receipt' => $receipt->id, 'token' => $token]) }}" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] px-4 py-2 rounded-lg font-bold transition-colors">Скачать PDF</a>
            </div>

            <div class="mt-6 text-gray-300">
                <div class="text-sm text-gray-400">Сумма</div>
                <div class="text-3xl font-bold text-white">{{ number_format((float) $receipt->amount, 2, '.', ' ') }} ₽</div>
            </div>

            <div class="mt-8">
                <a href="{{ route('receipts.public.verify', ['receipt' => $receipt->id, 'token' => $token]) }}" class="text-sm text-[#a6cb40] hover:underline">Проверить подлинность</a>
            </div>
        </div>
    </div>
</section>
@endsection
