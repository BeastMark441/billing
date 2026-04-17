<x-app-layout>
    <div class="mb-8">
        <a href="{{ route('dashboard.infrastructure') }}" class="text-gray-400 hover:text-white transition-colors flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Назад к каталогу
        </a>
        <h2 class="text-2xl font-bold text-white mb-2">Оформление заказа</h2>
        <p class="text-gray-400">Подтвердите параметры услуги перед заказом</p>
    </div>

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Выбранная услуга</h3>
                
                <div class="flex items-start justify-between mb-6 pb-6 border-b border-white/5">
                    <div>
                        <div class="text-sm text-gray-400 mb-1">{{ $service->category->name }} / {{ $service->subcategory->name ?? 'Общее' }}</div>
                        <h4 class="text-xl font-bold text-white mb-2">{{ $service->name }}</h4>
                        <div class="text-gray-300 text-sm">{{ $service->description }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-[#a6cb40] font-mono">{{ number_format($service->price, 2) }} ₽</div>
                        <div class="text-xs text-gray-500">в месяц</div>
                    </div>
                </div>

                @if($service->display_specifications && is_array($service->display_specifications))
                <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Технические характеристики</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($service->display_specifications as $key => $value)
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
                @endif
            </div>

            <!-- Configuration Form (Placeholder for future options like Location, OS, etc) -->
            <form id="order-form" action="{{ route('orders.store', $service) }}" method="POST">
                @csrf
                <!-- Add custom fields here if needed -->
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 sticky top-6">
                <h3 class="text-lg font-bold text-white mb-4">Итого</h3>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Услуга</span>
                        <span class="text-white">{{ number_format($service->price, 2) }} ₽</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Настройка</span>
                        <span class="text-white">0.00 ₽</span>
                    </div>
                    <div class="border-t border-white/5 pt-3 flex justify-between font-bold text-lg">
                        <span class="text-white">К оплате</span>
                        <span class="text-[#a6cb40]">{{ number_format($service->price, 2) }} ₽</span>
                    </div>
                </div>

                <button type="button" onclick="document.getElementById('confirmOrderModal').classList.remove('hidden')" class="w-full bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] font-bold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <span>Оплатить и создать</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </button>
                
                <p class="text-xs text-center text-gray-500 mt-4">
                    Нажимая кнопку, вы соглашаетесь с условиями предоставления услуг
                </p>
            </div>
        </div>
    </div>

    <!-- Order Confirmation Modal -->
    <div id="confirmOrderModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#1a1a20] rounded-2xl max-w-md w-full p-6 border border-white/10">
            <h3 class="text-xl font-bold text-white mb-4">Подтверждение заказа</h3>
            <p class="text-gray-400 text-sm mb-6">
                Вы собираетесь заказать услугу <strong>{{ $service->name }}</strong>.
                <br><br>
                Стоимость: <span class="text-white font-bold">{{ number_format($service->price, 2) }} ₽</span>
                <br>
                Ваш баланс: <span class="text-white font-bold">{{ number_format(Auth::user()->balance, 2) }} ₽</span>
                
                @if(Auth::user()->balance < $service->price)
                <div class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                    <p class="text-yellow-400 font-semibold mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Недостаточно средств
                    </p>
                    <p class="text-gray-400 text-xs mb-4">
                        Ваш баланс меньше стоимости услуги. Пожалуйста, пополните баланс на {{ number_format($service->price - Auth::user()->balance, 2) }} ₽.
                    </p>
                    <form action="{{ route('payments.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $service->price - Auth::user()->balance }}">
                        <button type="submit" class="w-full bg-[#3b82f6] hover:bg-[#2563eb] text-white font-bold py-2 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm">
                            <span>Пополнить через T-Bank</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </form>
                </div>
                @endif
            </p>

            <div class="flex gap-4">
                <button type="button" onclick="document.getElementById('confirmOrderModal').classList.add('hidden')" class="flex-1 bg-white/5 hover:bg-white/10 text-white font-bold py-3 rounded-xl transition-colors">
                    Отмена
                </button>
                @if(Auth::user()->balance >= $service->price)
                <button type="submit" form="order-form" class="flex-1 bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] font-bold py-3 rounded-xl transition-colors">
                    Подтверждаю
                </button>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
