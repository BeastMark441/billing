<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Чеки</h2>
                <p class="text-sm text-gray-400">Скачивание чеков и проверка подлинности.</p>
            </div>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="text-xs text-gray-500">Дата от</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>
                <div>
                    <label class="text-xs text-gray-500">Дата до</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>
                <div>
                    <label class="text-xs text-gray-500">Сумма от</label>
                    <input type="number" step="0.01" name="amount_from" value="{{ request('amount_from') }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>
                <div>
                    <label class="text-xs text-gray-500">Сумма до</label>
                    <input type="number" step="0.01" name="amount_to" value="{{ request('amount_to') }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>
                <div class="flex items-end gap-2">
                    <button class="w-full bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">Применить</button>
                    <a href="{{ route('dashboard.receipts.index') }}" class="w-full text-center bg-transparent border border-white/10 hover:bg-white/5 text-gray-200 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">Сброс</a>
                </div>
            </form>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-white/5">
                            <th class="px-6 py-3 font-medium">Номер</th>
                            <th class="px-6 py-3 font-medium">Тип</th>
                            <th class="px-6 py-3 font-medium">Сумма</th>
                            <th class="px-6 py-3 font-medium">Дата</th>
                            <th class="px-6 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($receipts as $receipt)
                            <tr class="text-sm hover:bg-white/5">
                                <td class="px-6 py-3 text-white font-medium">
                                    <a href="{{ route('dashboard.receipts.show', $receipt) }}" class="hover:underline">{{ $receipt->receipt_number }}</a>
                                </td>
                                <td class="px-6 py-3 text-gray-300">{{ $receipt->type === 'deposit' ? 'Пополнение' : 'Покупка' }}</td>
                                <td class="px-6 py-3 text-gray-300">{{ number_format((float) $receipt->amount, 2, '.', ' ') }} ₽</td>
                                <td class="px-6 py-3 text-gray-400">{{ $receipt->issued_at?->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('dashboard.receipts.download', $receipt) }}" class="text-[#a6cb40] hover:underline">PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">Чеков пока нет.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($receipts->hasPages())
                <div class="p-4 border-t border-white/10">{{ $receipts->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

