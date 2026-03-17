<x-app-layout>
    <div class="space-y-6 max-w-4xl">
        <div>
            <h2 class="text-2xl font-bold text-white">Чек {{ $receipt->receipt_number }}</h2>
            <p class="text-sm text-gray-400">{{ $receipt->issued_at?->format('d.m.Y H:i') }} · {{ $receipt->payment_method ?? '—' }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
                <div class="text-xs text-gray-400">Сумма</div>
                <div class="mt-2 text-2xl font-bold text-white">{{ number_format((float) $receipt->amount, 2, '.', ' ') }} ₽</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
                <div class="text-xs text-gray-400">Тип</div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $receipt->type === 'deposit' ? 'Пополнение' : 'Покупка' }}</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-xl p-5 flex items-center justify-between">
                <a href="{{ route('dashboard.receipts.download', $receipt) }}" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] px-5 py-3 rounded-xl font-bold transition-colors">Скачать PDF</a>
            </div>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-bold text-white">Публичная ссылка</h3>
            <p class="text-sm text-gray-400 mt-1">Можно отправить пользователю для скачивания и проверки.</p>
            @php($publicUrl = route('receipts.public.show', ['receipt' => $receipt->id, 'token' => $receipt->public_token]))
            <div class="mt-4 bg-white/5 border border-white/10 rounded-lg p-4 text-sm text-gray-300 break-all font-mono">{{ $publicUrl }}</div>
            <div class="mt-3">
                <a href="{{ route('receipts.public.verify', ['receipt' => $receipt->id, 'token' => $receipt->public_token]) }}" class="text-sm text-[#a6cb40] hover:underline">Проверка подлинности</a>
            </div>
        </div>
    </div>
</x-app-layout>

