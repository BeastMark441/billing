<x-admin-layout>
    <div x-data="{ tab: 'general' }">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Редактирование пользователя: {{ $user->full_name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition-colors">
                &larr; Назад к списку
            </a>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-white/10 mb-8 overflow-x-auto">
            <button @click="tab = 'general'" :class="{ 'border-blue-500 text-blue-500': tab === 'general', 'border-transparent text-gray-400 hover:text-white': tab !== 'general' }" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                Основная информация
            </button>
            <button @click="tab = 'balance'" :class="{ 'border-blue-500 text-blue-500': tab === 'balance', 'border-transparent text-gray-400 hover:text-white': tab !== 'balance' }" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                Баланс
            </button>
            <button @click="tab = 'security'" :class="{ 'border-blue-500 text-blue-500': tab === 'security', 'border-transparent text-gray-400 hover:text-white': tab !== 'security' }" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                Безопасность
            </button>
            <button @click="tab = 'notifications'" :class="{ 'border-blue-500 text-blue-500': tab === 'notifications', 'border-transparent text-gray-400 hover:text-white': tab !== 'notifications' }" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                Уведомления
            </button>
            <button @click="tab = 'services'" :class="{ 'border-blue-500 text-blue-500': tab === 'services', 'border-transparent text-gray-400 hover:text-white': tab !== 'services' }" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                Услуги
            </button>
            <button @click="tab = 'logs'" :class="{ 'border-blue-500 text-blue-500': tab === 'logs', 'border-transparent text-gray-400 hover:text-white': tab !== 'logs' }" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                Логи активности
            </button>
        </div>

        <!-- Tab Contents -->
        <div x-show="tab === 'general'" class="space-y-6">
            <!-- General Info Form -->
             <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Avatar (Initials) -->
                    <div class="col-span-full">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Аватар</label>
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div class="text-sm text-gray-500">
                                Аватар генерируется автоматически на основе имени.
                            </div>
                        </div>
                    </div>

                    <!-- Surname -->
                    <div>
                        <label for="surname" class="block text-sm font-medium text-gray-400 mb-2">Фамилия</label>
                        <input type="text" name="surname" id="surname" value="{{ old('surname', $user->surname) }}" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Имя</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Patronymic -->
                    <div>
                        <label for="patronymic" class="block text-sm font-medium text-gray-400 mb-2">Отчество</label>
                        <input type="text" name="patronymic" id="patronymic" value="{{ old('patronymic', $user->patronymic) }}" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-400 mb-2">Роль</label>
                        <select name="role" id="role" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Пользователь</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Администратор</option>
                        </select>
                    </div>

                    <!-- Block Status -->
                    <div class="col-span-full border-t border-white/10 pt-6 mt-2">
                        <h3 class="text-lg font-medium text-white mb-4">Блокировка</h3>
                        
                        <div class="flex items-center gap-4 mb-4">
                            <input type="checkbox" name="is_blocked" id="is_blocked" value="1" {{ $user->is_blocked ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-600 ring-offset-gray-800 focus:ring-2">
                            <label for="is_blocked" class="text-sm font-medium text-gray-400">Заблокировать пользователя</label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="blocked_until" class="block text-sm font-medium text-gray-400 mb-2">Заблокировать до (опционально)</label>
                                <input type="datetime-local" name="blocked_until" id="blocked_until" value="{{ $user->blocked_until ? $user->blocked_until->format('Y-m-d\TH:i') : '' }}" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                            </div>
                            <div>
                                <label for="blocked_reason" class="block text-sm font-medium text-gray-400 mb-2">Причина блокировки</label>
                                <textarea name="blocked_reason" id="blocked_reason" rows="1" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">{{ old('blocked_reason', $user->blocked_reason) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-white/10">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>

        <div x-show="tab === 'balance'" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Update Balance Form -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                        <h3 class="text-lg font-medium text-white mb-4">Управление балансом</h3>
                        <div class="text-3xl font-bold text-white mb-6">{{ number_format($user->balance, 2) }} ₽</div>
                        
                        <form action="{{ route('admin.users.balance', $user) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-400 mb-2">Тип операции</label>
                                <select name="type" id="type" required class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                                    <option value="admin_deposit">Пополнение (+)</option>
                                    <option value="admin_deduction">Списание (-)</option>
                                    <option value="bonus">Бонус (+)</option>
                                    <option value="penalty">Штраф (-)</option>
                                    <option value="refund">Возврат (+)</option>
                                    <option value="correction">Корректировка (Как есть)</option>
                                </select>
                            </div>

                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-400 mb-2">Сумма</label>
                                <input type="number" step="0.01" name="amount" id="amount" placeholder="0.00" required class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                                <p class="text-xs text-gray-500 mt-1">Для списания выберите тип "Списание" или "Штраф" и укажите положительную сумму.</p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-400 mb-2">Комментарий (опционально)</label>
                                <textarea name="description" id="description" rows="2" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors"></textarea>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                                Выполнить операцию
                            </button>
                        </form>
                    </div>
                </div>

                <!-- History -->
                <div class="lg:col-span-2">
                    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
                        <div class="p-6 border-b border-white/5">
                            <h3 class="text-lg font-medium text-white">История операций</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                                        <th class="px-6 py-4 font-medium">Дата</th>
                                        <th class="px-6 py-4 font-medium">Тип</th>
                                        <th class="px-6 py-4 font-medium">Сумма</th>
                                        <th class="px-6 py-4 font-medium">Админ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($balanceLogs as $log)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            {{ $log->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-white/10 text-white">
                                                {{ $log->type }}
                                            </span>
                                            @if($log->description)
                                                <div class="text-xs text-gray-500 mt-1">{{ $log->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-medium {{ $log->amount >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $log->amount >= 0 ? '+' : '' }}{{ number_format($log->amount, 2) }} ₽
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-400">
                                            {{ $log->admin ? $log->admin->name : 'System' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            История операций пуста
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($balanceLogs->hasPages())
                        <div class="p-4 border-t border-white/5">
                            {{ $balanceLogs->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'security'" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Password Change -->
                <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Смена пароля</h3>
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="surname" value="{{ $user->surname }}">
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="patronymic" value="{{ $user->patronymic }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        
                        @if($user->is_blocked)
                            <input type="hidden" name="is_blocked" value="1">
                        @endif
                        <input type="hidden" name="blocked_until" value="{{ $user->blocked_until }}">
                        <input type="hidden" name="blocked_reason" value="{{ $user->blocked_reason }}">
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Новый пароль</label>
                            <input type="password" name="password" id="password" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-2">Подтверждение пароля</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium text-sm">
                                Установить пароль
                            </button>
                            
                            <button form="send-reset-form" type="submit" class="text-sm text-blue-500 hover:text-blue-400 hover:underline">
                                Отправить ссылку сброса
                            </button>
                        </div>
                    </form>
                    <form id="send-reset-form" action="{{ route('admin.users.send-reset', $user) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>

                <!-- Email Verification -->
                <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Email верификация</h3>
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="text-sm text-gray-400">Статус:</div>
                        @if($user->hasVerifiedEmail())
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-500">Подтвержден</span>
                            <div class="text-xs text-gray-500">({{ $user->email_verified_at->format('d.m.Y H:i') }})</div>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-500">Не подтвержден</span>
                        @endif
                    </div>
                    
                    <div class="mb-4 p-3 bg-white/5 rounded-lg">
                        <div class="text-sm text-gray-400 mb-1">Привязанный Email:</div>
                        <div class="text-white font-mono">{{ $user->email }}</div>
                    </div>

                    <div class="space-y-3">
                        <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 border border-white/10 hover:bg-white/5 text-white rounded-lg transition-colors text-sm">
                                {{ $user->hasVerifiedEmail() ? 'Отменить подтверждение' : 'Подтвердить вручную' }}
                            </button>
                        </form>
                        
                        @if(!$user->hasVerifiedEmail())
                        <form action="{{ route('admin.users.send-verification', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 border border-white/10 hover:bg-white/5 text-white rounded-lg transition-colors text-sm">
                                Отправить ссылку подтверждения
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- IP Ban -->
                <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 col-span-full">
                    <h3 class="text-lg font-medium text-white mb-4">Блокировка IP</h3>
                    <p class="text-sm text-gray-400 mb-4">Последний известный IP: <span class="text-white font-mono">{{ $lastIp ?? 'Неизвестно' }}</span></p>
                    
                    <form action="{{ route('admin.users.ban-ip', $user) }}" method="POST" class="flex gap-4">
                        @csrf
                        <input type="text" name="ip" value="{{ $lastIp }}" placeholder="IP адрес" required class="flex-1 bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500 transition-colors">
                        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium whitespace-nowrap">
                            Заблокировать IP
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="tab === 'notifications'" class="space-y-6" style="display: none;">
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 max-w-2xl">
                <h3 class="text-lg font-medium text-white mb-4">Отправить уведомление</h3>
                
                <form action="{{ route('admin.users.notify', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="notif_type" class="block text-sm font-medium text-gray-400 mb-2">Тип уведомления</label>
                        <select name="type" id="notif_type" class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                            <option value="info">Info (Информация)</option>
                            <option value="success">Success (Успех)</option>
                            <option value="warning">Warning (Предупреждение)</option>
                            <option value="danger">Danger (Ошибка/Важно)</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-400 mb-2">Текст сообщения</label>
                        <textarea name="message" id="message" rows="4" required class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors"></textarea>
                    </div>

                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        Отправить
                    </button>
                </form>
            </div>
        </div>

        <div x-show="tab === 'services'" class="space-y-6" style="display: none;">
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.orders.create', ['user_id' => $user->id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Добавить услугу
                </a>
            </div>

            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 font-medium">ID</th>
                                <th class="px-6 py-4 font-medium">Услуга</th>
                                <th class="px-6 py-4 font-medium">Статус</th>
                                <th class="px-6 py-4 font-medium">Цена</th>
                                <th class="px-6 py-4 font-medium">Истекает</th>
                                <th class="px-6 py-4 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($user->orders as $order)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-400">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-white font-medium">{{ $order->service->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $order->status === 'active' ? 'bg-green-500/10 text-green-500' : 
                                          ($order->status === 'suspended' ? 'bg-yellow-500/10 text-yellow-500' : 
                                          ($order->status === 'cancelled' ? 'bg-gray-500/10 text-gray-500' : 'bg-blue-500/10 text-blue-500')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-white">{{ number_format($order->price, 2) }} ₽</td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $order->expires_at ? $order->expires_at->format('d.m.Y') : '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 hover:underline text-sm">Управление</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    У пользователя нет активных услуг
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="tab === 'logs'" class="space-y-6" style="display: none;">
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-white/5">
                    <h3 class="text-lg font-medium text-white">Журнал активности</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-medium">Действие</th>
                                <th class="px-6 py-4 font-medium">IP адрес</th>
                                <th class="px-6 py-4 font-medium">User Agent</th>
                                <th class="px-6 py-4 font-medium">Дата</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($logs as $log)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ $log->action }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400 font-mono">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">
                                    {{ $log->created_at->format('d.m.Y H:i:s') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Записей активности не найдено
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
    </div>
</x-admin-layout>
