<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">История финансов</h1>
            <p class="text-gray-400">Общий список финансовых операций всех пользователей</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.finance.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по email, имени или описанию..." 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="w-[200px]">
                <select name="type" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">Все типы</option>
                    <option value="admin_deposit" {{ request('type') == 'admin_deposit' ? 'selected' : '' }}>Пополнение</option>
                    <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Покупка</option>
                    <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Возврат</option>
                    <option value="renewal" {{ request('type') == 'renewal' ? 'selected' : '' }}>Продление</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Фильтровать
            </button>
            @if(request()->anyFilled(['search', 'type']))
                <a href="{{ route('admin.finance.index') }}" class="bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Сброс
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="bg-white/5 text-gray-200 uppercase text-xs">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Пользователь</th>
                        <th scope="col" class="px-6 py-3">Описание</th>
                        <th scope="col" class="px-6 py-3">Тип</th>
                        <th scope="col" class="px-6 py-3 text-right">Сумма</th>
                        <th scope="col" class="px-6 py-3 text-right">Дата</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs">
                            #{{ $log->id }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->user)
                                <a href="{{ route('admin.users.edit', $log->user) }}" class="text-white hover:text-blue-400 font-medium block">
                                    {{ $log->user->name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                            @else
                                <span class="text-gray-500 italic">Удален</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ $log->description }}
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
                                $color = $typeColors[$log->type] ?? 'bg-gray-500/10 text-gray-400';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ $log->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold {{ $log->amount >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $log->amount > 0 ? '+' : '' }}{{ number_format($log->amount, 2) }} ₽
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-xs">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Записей не найдено
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>