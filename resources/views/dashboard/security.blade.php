<x-app-layout>
    <div class="space-y-8">
        <div>
            <h2 class="text-2xl font-bold text-white">Безопасность</h2>
            <p class="text-sm text-gray-400">Настройки безопасности вашего аккаунта.</p>
        </div>

        <!-- Account Data Section -->
        <section class="bg-[#050508] border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 2-2 2h4c-1.5 0-2-1.116-2-2z"></path></svg>
                Данные аккаунта
            </h3>
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white/5 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-white">Телефон</div>
                        <div class="text-sm text-gray-400">{{ $user->phone ?? 'Не привязан' }}</div>
                    </div>
                    <button class="mt-2 sm:mt-0 text-sm font-medium text-[#a6cb40] hover:text-[#8eb330] transition-colors">
                        Изменить
                    </button>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white/5 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-white">Электронная почта</div>
                        <div class="text-sm text-gray-400">{{ $user->email }}</div>
                    </div>
                    <button class="mt-2 sm:mt-0 text-sm font-medium text-[#a6cb40] hover:text-[#8eb330] transition-colors">
                        Изменить
                    </button>
                </div>
            </div>
        </section>

        <!-- Login Methods Section -->
        <section class="bg-[#050508] border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Способ входа в аккаунт
            </h3>
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white/5 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-white">Пароль</div>
                        <div class="text-sm text-gray-400">Последнее изменение: никогда</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="mt-2 sm:mt-0 text-sm font-medium text-[#a6cb40] hover:text-[#8eb330] transition-colors">
                        Изменить пароль
                    </a>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white/5 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-white">Двухэтапная аутентификация</div>
                        <div class="text-sm text-gray-400">Защитите свой аккаунт дополнительным кодом</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="mt-2 sm:mt-0 text-sm font-medium text-[#a6cb40] hover:text-[#8eb330] transition-colors">
                        Настроить
                    </a>
                </div>
            </div>
        </section>

        <!-- Activity Log Section -->
        <section class="bg-[#050508] border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Активность
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-white/5">
                            <th class="px-4 py-3 font-medium">Действие</th>
                            <th class="px-4 py-3 font-medium">IP адрес</th>
                            <th class="px-4 py-3 font-medium">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($logs as $log)
                        <tr class="text-sm">
                            <td class="px-4 py-3 text-white">
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
                                    {{ $log->action }}
                                @endif
                                @if($log->details)
                                    <div class="text-xs text-gray-500">{{ $log->details }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 font-mono text-xs">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-4 py-3 text-gray-400">
                                {{ $log->created_at->format('d.m.Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-center text-gray-500">
                                История активности пуста
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 pt-4 border-t border-white/5 text-center">
                <a href="{{ route('dashboard.logs') }}" class="text-sm font-medium text-[#a6cb40] hover:text-[#8eb330] transition-colors">
                    Посмотреть всю историю
                </a>
            </div>
        </section>

        <!-- Delete Account Section -->
        <section class="bg-[#050508] border border-red-500/20 rounded-xl p-6">
            <h3 class="text-lg font-bold text-red-500 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Удаление аккаунта
            </h3>
            <div class="p-4 bg-red-500/5 border border-red-500/10 rounded-lg">
                <p class="text-sm text-gray-300 mb-4">
                    После удаления аккаунта все ваши ресурсы и данные будут безвозвратно удалены. Заявка на удаление будет обработана службой поддержки.
                </p>
                <button class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/50 rounded-md text-sm font-medium transition-colors">
                    Запросить удаление
                </button>
            </div>
        </section>
    </div>
</x-app-layout>
