<x-app-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Финансы</h2>
        <p class="text-gray-400">Управление балансом и история операций</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Balance Card -->
        <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-8 flex flex-col justify-between">
            <div>
                <div class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-1">Текущий баланс</div>
                <div class="text-4xl font-bold text-white font-mono">{{ number_format(Auth::user()->balance, 2, '.', ' ') }} ₽</div>
            </div>
            <div class="mt-6">
                <p class="text-xs text-gray-500 italic">Средства списываются автоматически при продлении активных услуг.</p>
            </div>
        </div>

        <!-- Top up Card -->
        <div class="lg:col-span-2 bg-[#0f0f13] border border-white/5 rounded-2xl p-8">
            <h3 class="text-xl font-bold text-white mb-6">Пополнить баланс</h3>
            
            <form action="{{ route('payments.create') }}" method="POST" class="max-w-md">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="number" name="amount" min="10" step="1" required placeholder="Сумма (₽)" 
                               class="w-full bg-[#050508] border border-white/10 rounded-xl px-4 py-3 text-white font-mono text-lg focus:outline-none focus:border-[#a6cb40] transition-colors appearance-none">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-mono">₽</span>
                    </div>
                    <button type="submit" class="bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] font-bold px-8 py-3 rounded-xl transition-all shadow-lg shadow-[#a6cb40]/10 flex items-center justify-center gap-2">
                        <span>Пополнить</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="button" onclick="this.form.amount.value=100" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-lg text-xs transition-colors border border-white/5">100 ₽</button>
                    <button type="button" onclick="this.form.amount.value=500" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-lg text-xs transition-colors border border-white/5">500 ₽</button>
                    <button type="button" onclick="this.form.amount.value=1000" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-lg text-xs transition-colors border border-white/5">1000 ₽</button>
                    <button type="button" onclick="this.form.amount.value=5000" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-lg text-xs transition-colors border border-white/5">5000 ₽</button>
                </div>
                <p class="mt-6 text-xs text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04m17.236 0a11.955 11.955 0 01-8.618 3.04m-7.236 0A11.955 11.955 0 0112 2.944a11.955 11.955 0 018.618 3.04M12 10a2 2 0 110-4 2 2 0 010 4zm0 0v10m0 0l-4-4m4 4l4-4"></path></svg>
                    Оплата через защищенный шлюз T-Bank. Комиссия 0%.
                </p>
            </form>
        </div>
    </div>

    <div class="space-y-8">
        <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
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
                                            'deposit' => 'bg-green-500/10 text-green-500',
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
                                            'deposit' => 'Пополнение',
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
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p>История операций пуста</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                <div class="px-6 py-4 bg-white/5 border-t border-white/5">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Pending Payments (Collapsible) -->
        @if(isset($pendingPayments) && $pendingPayments->count() > 0)
        <div x-data="{ open: false }" class="space-y-4">
            <button @click="open = !open" class="flex items-center justify-between w-full bg-[#0a0a0f] border border-white/10 rounded-xl px-6 py-4 text-left transition-colors hover:bg-white/5">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Платежи в обработке</h3>
                        <p class="text-xs text-gray-500">Ожидают подтверждения от банка ({{ $pendingPayments->count() }})</p>
                    </div>
                </div>
                <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
                <div class="bg-yellow-500/10 border border-yellow-500/20 text-yellow-200 px-4 py-3 rounded-xl text-sm">
                    Платёж появится на балансе после подтверждения банка. Если в течение 30 минут статус не изменится, обратитесь в поддержку.
                </div>

                <div class="bg-[#050508] border border-white/10 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-400">
                            <thead class="bg-white/5 text-gray-200 uppercase text-xs">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Дата</th>
                                    <th scope="col" class="px-6 py-3">Описание</th>
                                    <th scope="col" class="px-6 py-3">Статус</th>
                                    <th scope="col" class="px-6 py-3 text-right">Сумма</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($pendingPayments as $payment)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-6 py-4 font-medium text-white">Пополнение баланса (T-Bank)</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_color }} bg-white/5">
                                                {{ $payment->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-yellow-300">+{{ number_format((float) $payment->amount, 2, '.', ' ') }} ₽</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
