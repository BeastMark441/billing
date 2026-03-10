<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Личная информация</h2>
            <p class="text-sm text-gray-400">Информация о вашем аккаунте и персональные данные.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Account Number -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                <div class="text-sm text-gray-400 mb-1">Номер аккаунта</div>
                <div class="text-lg font-medium text-white flex items-center gap-2">
                    {{ $user->account_number ?? 'Не задан' }}
                    <button class="text-gray-500 hover:text-[#a6cb40] transition-colors" onclick="navigator.clipboard.writeText('{{ $user->account_number }}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Full Name -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                <div class="text-sm text-gray-400 mb-1">ФИО</div>
                <div class="text-lg font-medium text-white">{{ $user->name }}</div>
            </div>

            <!-- Email -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                <div class="text-sm text-gray-400 mb-1">Почта</div>
                <div class="text-lg font-medium text-white">{{ $user->email }}</div>
            </div>

            <!-- Phone -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                <div class="text-sm text-gray-400 mb-1">Мобильный телефон</div>
                <div class="text-lg font-medium text-white">{{ $user->phone ?? 'Не указан' }}</div>
            </div>

            <!-- UID -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6 md:col-span-2">
                <div class="text-sm text-gray-400 mb-1">UID</div>
                <div class="text-lg font-medium text-white font-mono flex items-center gap-2">
                    {{ $user->uid ?? 'Не сгенерирован' }}
                    @if($user->uid)
                    <button class="text-gray-500 hover:text-[#a6cb40] transition-colors" onclick="navigator.clipboard.writeText('{{ $user->uid }}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Role -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                <div class="text-sm text-gray-400 mb-1">Роль</div>
                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#a6cb40]/10 text-[#a6cb40]">
                    {{ $user->role_label }}
                </div>
            </div>

            <!-- Birth Date -->
            <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                <div class="text-sm text-gray-400 mb-1">Дата рождения</div>
                <div class="text-lg font-medium text-white">
                    {{ $user->birth_date ? $user->birth_date->format('d.m.Y') : 'Не указана' }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
