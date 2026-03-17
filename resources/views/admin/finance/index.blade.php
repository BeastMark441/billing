<x-admin-layout>
    <div x-data="{ deleteOneOpen: false, deleteAllOpen: false, deleteUrl: '', deleteId: '' }">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">История финансов</h1>
                <p class="text-gray-400">Общий список финансовых операций всех пользователей</p>
            </div>
            <div class="text-sm text-gray-400">Найдено: <span class="text-white font-semibold">{{ $total ?? $logs->total() }}</span></div>
        </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-200 px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-xl mb-6">Проверьте введённые данные.</div>
    @endif

    <!-- Filters -->
    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.finance.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по email, имени или описанию..." 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="w-[140px]">
                <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="User ID" 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="w-[200px]">
                <select name="type" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">Все типы</option>
                    <option value="admin_deposit" {{ request('type') == 'admin_deposit' ? 'selected' : '' }}>Пополнение</option>
                    <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Пополнение (T-Bank)</option>
                    <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Покупка</option>
                    <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Возврат</option>
                    <option value="renewal" {{ request('type') == 'renewal' ? 'selected' : '' }}>Продление</option>
                </select>
            </div>
            <div class="w-[160px]">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="w-[160px]">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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

        <div class="mt-4 flex flex-col md:flex-row gap-3">
            <button type="button" @click="deleteAllOpen = true" class="bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-200 px-4 py-2 rounded-lg font-medium transition-colors">
                Удалить записи по текущему фильтру
            </button>
            <div class="text-xs text-gray-500 md:ml-auto self-center">Удаление доступно только супер-администратору и требует подтверждения паролем.</div>
        </div>
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
                        <th scope="col" class="px-6 py-3 text-right">Действия</th>
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
                        <td class="px-6 py-4 text-right">
                            <button
                                type="button"
                                class="text-red-300 hover:text-red-200 text-sm"
                                @click="deleteUrl = '{{ route('admin.finance.logs.destroy', $log) }}'; deleteId = '{{ $log->id }}'; deleteOneOpen = true"
                            >
                                Удалить
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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

    <div x-show="deleteOneOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
        <div class="bg-[#1a1a20] border border-white/10 rounded-2xl p-6 w-full max-w-lg">
            <div class="text-lg font-bold text-white">Удалить запись</div>
            <div class="mt-2 text-sm text-gray-400">Будет удалена запись финансовой истории #<span class="text-white" x-text="deleteId"></span>.</div>

            <form method="POST" x-bind:action="deleteUrl" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label class="text-xs text-gray-500">Введите DELETE</label>
                    <input name="confirm" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" placeholder="DELETE" />
                </div>

                <div>
                    <label class="text-xs text-gray-500">Пароль супер-админа</label>
                    <input name="password" type="password" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="deleteOneOpen = false" class="px-4 py-2 text-gray-300 hover:text-white">Отмена</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg">Удалить</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteAllOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
        <div class="bg-[#1a1a20] border border-white/10 rounded-2xl p-6 w-full max-w-lg">
            <div class="text-lg font-bold text-white">Удаление по фильтру</div>
            <div class="mt-2 text-sm text-gray-400">Будут удалены все записи, подходящие под текущий фильтр. Найдено: <span class="text-white font-semibold">{{ $total ?? $logs->total() }}</span>.</div>

            <form method="POST" action="{{ route('admin.finance.destroy') }}" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')

                <input type="hidden" name="search" value="{{ request('search') }}" />
                <input type="hidden" name="type" value="{{ request('type') }}" />
                <input type="hidden" name="user_id" value="{{ request('user_id') }}" />
                <input type="hidden" name="date_from" value="{{ request('date_from') }}" />
                <input type="hidden" name="date_to" value="{{ request('date_to') }}" />
                <input type="hidden" name="amount_from" value="{{ request('amount_from') }}" />
                <input type="hidden" name="amount_to" value="{{ request('amount_to') }}" />

                <div>
                    <label class="text-xs text-gray-500">Введите DELETE FINANCE</label>
                    <input name="confirm" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" placeholder="DELETE FINANCE" />
                </div>

                <div>
                    <label class="text-xs text-gray-500">Пароль супер-админа</label>
                    <input name="password" type="password" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="deleteAllOpen = false" class="px-4 py-2 text-gray-300 hover:text-white">Отмена</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg">Удалить</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</x-admin-layout>
