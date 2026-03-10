<x-app-layout>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="text-2xl font-bold text-white">Заказ #{{ $order->id }}</h2>
                
                @php
                    $statusClasses = [
                        'pending' => 'bg-yellow-500/10 text-yellow-500',
                        'paid' => 'bg-blue-500/10 text-blue-500',
                        'active' => 'bg-green-500/10 text-green-500',
                        'suspended' => 'bg-red-500/10 text-red-500',
                        'cancelled' => 'bg-gray-500/10 text-gray-500',
                        'failed' => 'bg-red-500/10 text-red-500',
                    ];
                    $statusLabels = [
                        'pending' => 'Ожидает оплаты',
                        'paid' => 'Оплачен',
                        'active' => 'Активен',
                        'suspended' => 'Приостановлен',
                        'cancelled' => 'Отменен',
                        'failed' => 'Ошибка',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                    {{ $statusLabels[$order->status] ?? $order->status }}
                </span>
            </div>
            <p class="text-gray-400 ml-10">{{ $order->service->name }} ({{ $order->service->category->name }})</p>
        </div>
        
        @if($order->status === 'active' && $order->pterodactyl_server_identifier)
        <a href="{{ config('services.pterodactyl.url') }}/server/{{ $order->pterodactyl_server_identifier }}" target="_blank" class="bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] font-bold px-6 py-2.5 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            Перейти в панель
        </a>
        @endif
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
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Server Info -->
            @if($order->status === 'active' || $order->status === 'suspended')
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    Информация о сервере
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">IP Адрес</div>
                        <div class="text-white font-mono text-lg select-all bg-black/20 px-3 py-1.5 rounded border border-white/5 inline-block">
                            {{ $order->server_ip }}:{{ $order->server_port }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Идентификатор (UUID)</div>
                        <div class="text-gray-300 font-mono text-sm break-all">
                            {{ $order->pterodactyl_server_identifier }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Имя сервера</div>
                        <div class="text-white font-medium">
                            {{ $order->service->name }} #{{ $order->id }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Локация</div>
                        <div class="text-white font-medium">
                            Стандартная (Node 1)
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Technical Specs -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                    Технические характеристики
                </h3>
                
                @if($order->service->specifications && is_array($order->service->specifications))
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($order->service->specifications as $key => $value)
                    <div class="bg-white/5 rounded-lg p-3 border border-white/5">
                        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ $key }}</div>
                        <div class="text-white font-bold font-mono">
                            @if(is_array($value))
                                {{ json_encode($value) }}
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 italic">Спецификации не указаны для этого тарифа.</p>
                @endif
            </div>

            <!-- Description -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Описание тарифа</h3>
                <div class="prose prose-invert max-w-none text-gray-300">
                    {{ $order->service->description }}
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Payment Info -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Детали оплаты</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-gray-400">Стоимость</span>
                        <span class="text-white font-bold text-lg">{{ number_format($order->price, 2) }} ₽</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-gray-400">Дата создания</span>
                        <span class="text-white text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @if($order->paid_at)
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-gray-400">Оплачено</span>
                        <span class="text-white text-sm">{{ $order->paid_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                    @if($order->expires_at)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-400">Истекает</span>
                        <span class="text-white text-sm">{{ $order->expires_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @endif
                </div>

                @if($order->status === 'pending')
                <div class="mt-6">
                    <form action="#" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-colors">
                            Оплатить заказ
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- History/Logs (Placeholder) -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">История</h3>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 mt-2"></div>
                        <div>
                            <div class="text-sm text-white">Статус изменен на <span class="text-green-400">{{ $order->status }}</span></div>
                            <div class="text-xs text-gray-500">{{ $order->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-2"></div>
                        <div>
                            <div class="text-sm text-white">Заказ создан</div>
                            <div class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
