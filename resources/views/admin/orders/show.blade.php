<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Заказ #{{ $order->id }}</h1>
            <p class="text-gray-400">Управление заказом пользователя {{ $order->user->email }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-white transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <!-- Edit Form -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Редактирование заказа</h3>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Статус</label>
                            <select name="status" id="status" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="active" {{ $order->status === 'active' ? 'selected' : '' }}>Активен (Active)</option>
                                <option value="suspended" {{ $order->status === 'suspended' ? 'selected' : '' }}>Приостановлен (Suspended)</option>
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Ожидает оплаты (Pending)</option>
                                <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Оплачен (Paid)</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Отменен (Cancelled)</option>
                                <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Ошибка (Failed)</option>
                            </select>
                            <p class="text-[10px] text-gray-500 mt-1">Изменение статуса на 'suspended'/'active' синхронизируется с Pterodactyl</p>
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Стоимость продления (₽)</label>
                            <input type="number" name="price" id="price" value="{{ $order->price }}" step="0.01" min="0" 
                                   class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>

                        <!-- Expires At -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-300 mb-2">Дата истечения</label>
                            <input type="date" name="expires_at" id="expires_at" value="{{ $order->expires_at ? $order->expires_at->format('Y-m-d') : '' }}" 
                                   class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>

                    @if($order->status === 'failed' && $order->last_error)
                    <div class="mb-4 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                        <div class="text-xs font-bold text-red-400 uppercase tracking-wider mb-2">Текст ошибки установки</div>
                        <pre class="text-xs text-red-300 whitespace-pre-wrap font-mono">{{ $order->last_error }}</pre>
                    </div>
                    @endif

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>

            <!-- Server Info -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Информация о сервере</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="text-gray-500 mb-1">Pterodactyl Server ID</div>
                        <div class="text-white font-mono bg-black/20 px-2 py-1 rounded inline-block">{{ $order->pterodactyl_server_id ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">UUID</div>
                        <div class="text-white font-mono break-all">{{ $order->pterodactyl_server_identifier ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">IP адрес</div>
                        <div class="text-white font-mono">{{ $order->server_ip ?? '-' }}:{{ $order->server_port ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">Услуга</div>
                        <div class="text-white">
                            <a href="{{ route('admin.infrastructure.services.edit', $order->service) }}" class="text-blue-400 hover:underline">
                                {{ $order->service->name }}
                            </a>
                        </div>
                    </div>
                </div>
                
                @if($order->pterodactyl_server_identifier)
                <div class="mt-6 pt-6 border-t border-white/5">
                    <a href="{{ config('services.pterodactyl.url') }}/admin/servers/view/{{ $order->pterodactyl_server_id }}" target="_blank" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Открыть в Pterodactyl
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="space-y-6">
            <!-- Order Actions -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Действия</h3>
                <div class="space-y-3">
                    <!-- Change Plan Button -->
                    <button type="button" onclick="document.getElementById('changePlanModal').classList.remove('hidden')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors text-sm">
                        Сменить тариф
                    </button>

                    <!-- Refund Button -->
                    <button type="button" onclick="document.getElementById('refundModal').classList.remove('hidden')" class="w-full bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-500 border border-yellow-500/20 font-bold py-2 rounded-lg transition-colors text-sm">
                        Оформить возврат
                    </button>
                </div>
            </div>

            <div class="bg-[#0f0f13] border border-red-500/20 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-red-400 mb-4">Опасная зона</h3>
                <p class="text-gray-400 text-xs mb-4">Удаление заказа также попытается удалить сервер в панели Pterodactyl. Это действие необратимо.</p>
                
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Вы уверены? Сервер будет удален безвозвратно.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 font-bold py-3 rounded-lg transition-colors">
                        Удалить заказ и сервер
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Plan Modal -->
    <div id="changePlanModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#1a1a20] rounded-2xl max-w-md w-full p-6 border border-white/10">
            <h3 class="text-xl font-bold text-white mb-4">Смена тарифа</h3>
            <p class="text-gray-400 text-sm mb-6">Выберите новый тариф. Обратите внимание, что ресурсы сервера в Pterodactyl будут обновлены автоматически.</p>
            
            <form method="POST" action="{{ route('admin.orders.change-plan', $order) }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Новый тариф</label>
                    <select name="service_id" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white">
                        @foreach(\App\Models\InfrastructureService::where('is_active', true)->get() as $service)
                            <option value="{{ $service->id }}" {{ $order->service_id == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} ({{ number_format($service->price, 0) }} ₽)
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('changePlanModal').classList.add('hidden')" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                        Отмена
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Refund Modal -->
    <div id="refundModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#1a1a20] rounded-2xl max-w-md w-full p-6 border border-white/10">
            <h3 class="text-xl font-bold text-white mb-4">Оформление возврата</h3>
            
            @php
                $daysRemaining = $order->expires_at && $order->expires_at->isFuture() ? floor(now()->diffInDays($order->expires_at)) : 0;
                $dailyPrice = $order->price / 30; // Approx
                $refundAmount = round($daysRemaining * $dailyPrice, 2);
            @endphp
            
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mb-6">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-400 text-sm">Дней осталось:</span>
                    <span class="text-white font-mono">{{ $daysRemaining }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-400 text-sm">Стоимость в день:</span>
                    <span class="text-white font-mono">~{{ number_format($dailyPrice, 2) }} ₽</span>
                </div>
                <div class="border-t border-blue-500/20 my-2 pt-2 flex justify-between">
                    <span class="text-blue-300 font-bold">Рекомендуемый возврат:</span>
                    <span class="text-blue-300 font-bold font-mono">{{ number_format($refundAmount, 2) }} ₽</span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.orders.refund', $order) }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Сумма возврата (₽)</label>
                    <input type="number" name="amount" value="{{ $refundAmount }}" step="0.01" min="0" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Тип возврата</label>
                    <select name="type" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white">
                        <option value="balance">На баланс аккаунта</option>
                        <option value="external">На карту (T-Bank/Внешний)</option>
                    </select>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('refundModal').classList.add('hidden')" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                        Отмена
                    </button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded-lg transition-colors">
                        Подтвердить возврат
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
