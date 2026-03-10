<x-admin-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Панель администратора</h1>
        <p class="text-gray-400">Добро пожаловать в панель управления NODEUM.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stats Card -->
        <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-white">Пользователи</h3>
                <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-white">{{ \App\Models\User::count() }}</div>
            <p class="text-sm text-gray-500 mt-1">Всего зарегистрировано</p>
        </div>

        <!-- Stats Card -->
        <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-white">Тикеты</h3>
                <div class="p-2 bg-yellow-500/10 rounded-lg text-yellow-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-white">{{ \App\Models\Ticket::where('status', '!=', 'closed')->count() }}</div>
            <p class="text-sm text-gray-500 mt-1">Активные тикеты</p>
        </div>

        <!-- Stats Card -->
        <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-white">Баланс системы</h3>
                <div class="p-2 bg-green-500/10 rounded-lg text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format(\App\Models\User::sum('balance'), 2) }} ₽</div>
            <p class="text-sm text-gray-500 mt-1">Общий баланс пользователей</p>
        </div>
    </div>
</x-admin-layout>
