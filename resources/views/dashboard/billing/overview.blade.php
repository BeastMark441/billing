<x-app-layout>
    <div class="space-y-8">
        <div>
            <h2 class="text-2xl font-bold text-white">Обзор биллинга</h2>
            <p class="text-sm text-gray-400">Управление балансом и финансами.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Balance Card -->
            <div class="lg:col-span-2 bg-gradient-to-br from-[#0a0a0f] to-[#0f0f16] border border-white/10 rounded-xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#a6cb40]/5 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/3"></div>
                
                <div class="relative z-10">
                    <div class="text-sm text-gray-400 mb-2">Текущий баланс</div>
                    <div class="text-5xl font-bold text-white mb-8">{{ number_format($user->balance, 2, '.', ' ') }} ₽</div>
                    
                    <div class="flex flex-wrap gap-4">
                        <button onclick="document.getElementById('topUpModal').classList.remove('hidden')" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] px-6 py-3 rounded-lg font-bold transition-all shadow-[0_0_15px_rgba(166,203,64,0.2)] hover:shadow-[0_0_20px_rgba(166,203,64,0.4)] flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Пополнить баланс
                        </button>
                    </div>
                </div>
            </div>

    <!-- Top Up Modal -->
    <div id="topUpModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#1a1a20] rounded-2xl max-w-md w-full p-6 border border-white/10">
            <h3 class="text-xl font-bold text-white mb-4">Пополнение баланса</h3>
            <p class="text-gray-400 text-sm mb-6">Введите сумму пополнения. Вы будете перенаправлены на защищенную страницу оплаты T-Bank.</p>
            
            <form method="POST" action="{{ route('payments.create') }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Сумма (₽)</label>
                    <input type="number" name="amount" min="10" max="30000" step="1" required placeholder="500" 
                           class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white text-lg font-bold focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50">
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Мин: 10 ₽</span>
                        <span>Макс: 30 000 ₽</span>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('topUpModal').classList.add('hidden')" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                        Отмена
                    </button>
                    <button type="submit" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold py-2 px-6 rounded-lg transition-colors">
                        Перейти к оплате
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

            <!-- Promo Code Card -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6 flex flex-col justify-center">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Активация промокода
                </h3>
                <p class="text-sm text-gray-400 mb-4">
                    Есть промокод? Введите его ниже, чтобы получить бонус на баланс или скидку.
                </p>
                <div class="flex gap-2">
                    <input type="text" placeholder="PROMO2025" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-sm placeholder-gray-600">
                    <button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Expenses Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">История финансов</h3>
                
                <!-- Filters -->
                <form method="GET" action="{{ route('dashboard.billing') }}" class="flex items-center gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-1.5 text-white text-xs focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40]">
                    <span class="text-gray-500 text-xs">-</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-1.5 text-white text-xs focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40]">
                    <button type="submit" class="bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>

            <div class="bg-[#050508] border border-white/10 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-400">
                        <thead class="bg-white/5 text-gray-200 uppercase text-xs">
                            <tr>
                                <th scope="col" class="px-6 py-3">Дата</th>
                                <th scope="col" class="px-6 py-3">Описание</th>
                                <th scope="col" class="px-6 py-3">Тип</th>
                                <th scope="col" class="px-6 py-3 text-right">Сумма</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($transactions as $transaction)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $transaction->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-white">
                                    {{ $transaction->description ?? 'Без описания' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $typeColors = [
                                            'admin_deposit' => 'bg-green-500/10 text-green-500',
                                            'bonus' => 'bg-purple-500/10 text-purple-500',
                                            'refund' => 'bg-blue-500/10 text-blue-500',
                                            'correction' => 'bg-yellow-500/10 text-yellow-500',
                                            'penalty' => 'bg-red-500/10 text-red-500',
                                            'expense' => 'bg-gray-500/10 text-gray-400',
                                            'renewal' => 'bg-blue-500/10 text-blue-400',
                                            'purchase' => 'bg-green-500/10 text-green-500',
                                        ];
                                        $typeLabels = [
                                            'admin_deposit' => 'Пополнение',
                                            'bonus' => 'Бонус',
                                            'refund' => 'Возврат',
                                            'correction' => 'Корректировка',
                                            'penalty' => 'Штраф',
                                            'expense' => 'Списание',
                                            'renewal' => 'Продление',
                                            'purchase' => 'Покупка',
                                        ];
                                        $colorClass = $typeColors[$transaction->type] ?? 'bg-gray-500/10 text-gray-400';
                                        $label = $typeLabels[$transaction->type] ?? ucfirst($transaction->type);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold {{ $transaction->amount >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 2, '.', ' ') }} ₽
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    Операций за выбранный период не найдено.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($transactions->hasPages())
                <div class="p-4 border-t border-white/10">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
