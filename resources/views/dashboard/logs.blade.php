<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">История активности</h2>
                <p class="text-sm text-gray-400">Полный журнал действий вашего аккаунта.</p>
            </div>
            <a href="{{ route('dashboard.security') }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                &larr; Назад к безопасности
            </a>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-white/5">
                            <th class="px-6 py-4 font-medium">Действие</th>
                            <th class="px-6 py-4 font-medium">Детали</th>
                            <th class="px-6 py-4 font-medium">IP адрес / User Agent</th>
                            <th class="px-6 py-4 font-medium">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($logs as $log)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-white font-medium">
                                    @if($log->action === 'login')
                                        Вход в аккаунт
                                    @elseif($log->action === 'register')
                                        Регистрация
                                    @elseif($log->action === 'password_reset')
                                        Сброс пароля
                                    @elseif($log->action === 'password_update')
                                        Смена пароля
                                    @elseif($log->action === 'email_update')
                                        Смена Email
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $log->details ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                <div class="font-mono text-xs text-white">{{ $log->ip_address }}</div>
                                <div class="text-xs opacity-50 truncate max-w-xs" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400 whitespace-nowrap">
                                {{ $log->created_at->format('d.m.Y H:i:s') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                История активности пуста
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
    </div>
</x-app-layout>
