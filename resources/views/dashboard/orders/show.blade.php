<x-app-layout>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="text-2xl font-bold text-white">Заказ #{{ $order->id }}</h2>
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status_color }} bg-white/5">
                    {{ $order->status_label }}
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
            <!-- Actions -->
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Управление</h3>
                <div class="space-y-3">
                    @if($order->status === 'cart')
                    <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                        <p class="text-sm text-yellow-500">
                            Этот заказ находится в корзине. Оплатите его, чтобы получить доступ к управлению услугой.
                        </p>
                    </div>
                    <a href="{{ route('cart.index') }}" class="block w-full text-center px-4 py-3 bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] font-bold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Перейти к оформлению
                    </a>
                    @else
                    <!-- Auto Renewal Toggle (Only if NOT cancelled/failed) -->
                    @if(!in_array($order->status, ['cancelled', 'failed', 'cart']))
                    <form method="POST" action="{{ route('orders.auto-renewal', $order) }}" class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/5">
                        @csrf
                        <div class="text-sm font-medium text-white">Автопродление</div>
                        <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-[#0f0f13] {{ $order->auto_renewal ? 'bg-blue-600' : 'bg-gray-700' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $order->auto_renewal ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </form>
                    @endif

                    <!-- Manual Renew Button (Only if suspended or active) -->
                    @if($order->status === 'suspended' || $order->status === 'active')
                    <button type="button" onclick="document.getElementById('renewModal').classList.remove('hidden')" class="w-full text-center px-4 py-3 {{ $order->status === 'suspended' ? 'bg-green-600 hover:bg-green-700 text-white font-bold' : 'bg-white/5 hover:bg-white/10 text-white font-medium border border-white/10' }} rounded-lg transition-colors flex items-center justify-center gap-2">
                        @if($order->status === 'suspended')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Возобновить работу
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Продлить вручную
                        @endif
                    </button>
                    @endif

                    <!-- Create Ticket -->
                    <a href="{{ route('dashboard.tickets.create', ['order_id' => $order->id]) }}" class="block w-full text-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Создать тикет
                    </a>

                    <!-- Request Cancellation (Only if not already cancelled/failed) -->
                    @if(!in_array($order->status, ['cancelled', 'failed', 'cart']))
                    <a href="{{ route('dashboard.tickets.create', ['order_id' => $order->id, 'subject' => 'Отмена/Возврат заказа #' . $order->id]) }}" class="block w-full text-center px-4 py-3 bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Запросить отмену
                    </a>
                    
                    <!-- Change Plan (Only if active/suspended) -->
                    <a href="{{ route('dashboard.tickets.create', ['order_id' => $order->id, 'subject' => 'Смена тарифа для заказа #' . $order->id]) }}" class="block w-full text-center px-4 py-3 bg-white/5 hover:bg-white/10 text-white border border-white/10 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Смена тарифа
                    </a>
                    @endif
                    @endif
                </div>
            </div>

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

            <!-- Status History -->
            @if($order->statusHistory->count() > 0)
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">История статусов</h3>
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-white/10 before:to-transparent max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($order->statusHistory as $history)
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white/10 bg-[#0f0f13] shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                            @if($history->status_to === 'active')
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            @elseif($history->status_to === 'failed')
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            @elseif($history->status_to === 'suspended')
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            @else
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            @endif
                        </div>
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border border-white/5 bg-white/[0.02]">
                            <div class="flex items-center justify-between space-x-2 mb-1">
                                <div class="font-bold text-white text-sm">
                                    {{ ucfirst($history->status_to) }}
                                </div>
                                <time class="font-mono text-xs text-gray-500">{{ $history->created_at->format('d.m.Y H:i') }}</time>
                            </div>
                            @if($history->comment)
                            <div class="text-xs text-gray-400">
                                {{ $history->comment }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- Renew Confirmation Modal -->
    <div id="renewModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#1a1a20] rounded-2xl max-w-md w-full p-6 border border-white/10">
            <h3 class="text-xl font-bold text-white mb-4">Подтверждение продления</h3>
            <p class="text-gray-400 text-sm mb-6">
                Вы собираетесь продлить услугу <strong>{{ $order->service->name }}</strong> на 30 дней.
                <br><br>
                Стоимость: <span class="text-white font-bold">{{ number_format($order->price, 2) }} ₽</span>
                <br>
                Ваш баланс: <span class="text-white font-bold">{{ number_format(Auth::user()->balance, 2) }} ₽</span>
            </p>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('renewModal').classList.add('hidden')" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                    Отмена
                </button>
                <form method="POST" action="{{ route('orders.renew', $order) }}">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                        Подтвердить и оплатить
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
