<x-admin-layout>
    <div class="space-y-6" x-data="{ userDeleteOpen: false, allDeleteOpen: false }">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Логи</h1>
                <p class="text-sm text-gray-400">Поиск, фильтрация и безопасная очистка логов.</p>
            </div>
            <div class="text-sm text-gray-400">Найдено: <span class="text-white font-semibold">{{ $total }}</span></div>
        </div>

        @if (session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-200 px-4 py-3 rounded-xl">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-xl">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-xl">Проверьте введённые данные.</div>
        @endif

        <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
            <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-1">
                    <label class="text-xs text-gray-500">Источник</label>
                    <select name="source" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm">
                        <option value="audit" {{ ($filters['source'] ?? 'audit') === 'audit' ? 'selected' : '' }}>AuditLog</option>
                        <option value="user" {{ ($filters['source'] ?? 'audit') === 'user' ? 'selected' : '' }}>UserLog</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs text-gray-500">User ID</label>
                    <input name="user_id" value="{{ $filters['user_id'] }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" placeholder="например 12" />
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs text-gray-500">Action</label>
                    <input name="action" value="{{ $filters['action'] }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" placeholder="auth_login" />
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs text-gray-500">Severity (Audit)</label>
                    <select name="severity" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm">
                        <option value="" {{ empty($filters['severity']) ? 'selected' : '' }}>Любая</option>
                        <option value="info" {{ ($filters['severity'] ?? '') === 'info' ? 'selected' : '' }}>info</option>
                        <option value="warning" {{ ($filters['severity'] ?? '') === 'warning' ? 'selected' : '' }}>warning</option>
                        <option value="error" {{ ($filters['severity'] ?? '') === 'error' ? 'selected' : '' }}>error</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs text-gray-500">Поиск</label>
                    <input name="search" value="{{ $filters['search'] }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" placeholder="ip / object / correlation" />
                </div>
                <div class="md:col-span-1 flex items-end gap-2">
                    <button type="submit" class="w-full bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">Применить</button>
                    <a href="{{ route('admin.logs.index') }}" class="w-full text-center bg-transparent border border-white/10 hover:bg-white/5 text-gray-200 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">Сброс</a>
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs text-gray-500">Дата от</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs text-gray-500">Дата до</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                </div>
            </form>

            <div class="mt-5 flex flex-col md:flex-row gap-3">
                <button type="button" @click="userDeleteOpen = true" class="bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-200 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    Удалить логи пользователя по фильтру
                </button>
                <button type="button" @click="allDeleteOpen = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    Очистить все системные логи
                </button>
                <div class="text-xs text-gray-500 md:ml-auto self-center">Удаление доступно только супер-администратору и требует подтверждения паролем.</div>
            </div>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-white/5">
                            <th class="px-6 py-3 font-medium">ID</th>
                            <th class="px-6 py-3 font-medium">User</th>
                            <th class="px-6 py-3 font-medium">Action</th>
                            <th class="px-6 py-3 font-medium">Details</th>
                            <th class="px-6 py-3 font-medium">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($logs as $log)
                            <tr class="text-sm hover:bg-white/5">
                                <td class="px-6 py-3 text-gray-400">{{ $log->id }}</td>
                                <td class="px-6 py-3 text-gray-300">{{ $log->user_id ?? '—' }}</td>
                                <td class="px-6 py-3 text-white font-medium">{{ $log->action }}</td>
                                <td class="px-6 py-3 text-gray-400">
                                    @if(($filters['source'] ?? 'audit') === 'audit')
                                        <div class="text-xs text-gray-500">{{ $log->severity }} · {{ $log->object_type }} {{ $log->object_id ? '#'.$log->object_id : '' }}</div>
                                        <div class="mt-1 text-xs text-gray-400 break-all">{{ $log->correlation_id }}</div>
                                    @else
                                        <div class="text-xs text-gray-400 line-clamp-2">{{ $log->details }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">Записей не найдено.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-white/10">{{ $logs->links() }}</div>
            @endif
        </div>

        <div x-show="userDeleteOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
            <div class="bg-[#1a1a20] border border-white/10 rounded-2xl p-6 w-full max-w-lg">
                <div class="text-lg font-bold text-white">Подтверждение удаления</div>
                <div class="mt-2 text-sm text-gray-400">Будут удалены логи выбранного пользователя по текущему фильтру. Найдено: <span class="text-white font-semibold">{{ $total }}</span>.</div>

                <form method="POST" action="{{ route('admin.logs.user.destroy') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="source" value="{{ $filters['source'] }}" />
                    <input type="hidden" name="user_id" value="{{ $filters['user_id'] }}" />
                    <input type="hidden" name="action" value="{{ $filters['action'] }}" />
                    <input type="hidden" name="severity" value="{{ $filters['severity'] }}" />
                    <input type="hidden" name="search" value="{{ $filters['search'] }}" />
                    <input type="hidden" name="date_from" value="{{ $filters['date_from'] }}" />
                    <input type="hidden" name="date_to" value="{{ $filters['date_to'] }}" />

                    <div>
                        <label class="text-xs text-gray-500">Введите DELETE</label>
                        <input name="confirm" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" placeholder="DELETE" />
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">Пароль супер-админа</label>
                        <input name="password" type="password" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="userDeleteOpen = false" class="px-4 py-2 text-gray-300 hover:text-white">Отмена</button>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg">Удалить</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="allDeleteOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
            <div class="bg-[#1a1a20] border border-white/10 rounded-2xl p-6 w-full max-w-lg">
                <div class="text-lg font-bold text-white">Очистка всех логов</div>
                <div class="mt-2 text-sm text-gray-400">Операция удалит все записи AuditLog и UserLog. Действие необратимо.</div>

                <form method="POST" action="{{ route('admin.logs.destroy') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label class="text-xs text-gray-500">Введите DELETE ALL</label>
                        <input name="confirm" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" placeholder="DELETE ALL" />
                    </div>

                    <div>
                        <label class="text-xs text-gray-500">Пароль супер-админа</label>
                        <input name="password" type="password" required class="mt-1 w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-3 py-2 text-white text-sm" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="allDeleteOpen = false" class="px-4 py-2 text-gray-300 hover:text-white">Отмена</button>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg">Очистить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

