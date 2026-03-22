<x-app-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Ваша корзина</h2>
        <p class="text-gray-400">Управляйте выбранными услугами перед оформлением</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            @forelse($cartItems as $item)
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 hover:border-white/10 transition-colors">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ $item->service->category->name }}</div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ $item->service->name }}</h3>
                        <p class="text-sm text-gray-400 mb-4">{{ $item->service->description }}</p>
                        
                        <div class="flex flex-wrap gap-2">
                            @foreach($item->service->specifications ?? [] as $key => $value)
                            <span class="text-[10px] bg-white/5 text-gray-400 px-2 py-1 rounded border border-white/5 uppercase">
                                {{ $key }}: <span class="text-gray-200">{{ is_array($value) ? json_encode($value) : $value }}</span>
                            </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end gap-4 min-w-[150px]">
                        <div class="text-2xl font-bold text-[#a6cb40] font-mono">{{ number_format($item->price, 2) }} ₽</div>
                        
                        <div class="flex gap-2 w-full">
                            <form action="{{ route('cart.remove', $item) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Удалить
                                </button>
                            </form>
                            <a href="{{ route('orders.create', $item->service) }}" class="flex-1 bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] py-2 rounded-lg text-sm font-bold transition-colors text-center">
                                Оформить
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4 text-gray-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Корзина пуста</h3>
                <p class="text-gray-500 mb-6">Вы еще не добавили ни одной услуги в корзину.</p>
                <a href="{{ route('dashboard.infrastructure') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-colors">
                    Перейти к каталогу
                </a>
            </div>
            @endforelse
        </div>

        <div class="space-y-6">
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-blue-400 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Памятка
                </h3>
                <div class="space-y-4 text-sm text-gray-400">
                    <p>
                        <span class="text-white font-semibold">Срок хранения:</span> Товары хранятся в корзине <span class="text-blue-400 font-bold">7 дней</span> с момента добавления.
                    </p>
                    <p>
                        <span class="text-white font-semibold">Автоудаление:</span> По истечении этого срока заказ будет автоматически удален из вашей корзины.
                    </p>
                    <p>
                        <span class="text-white font-semibold">Наличие:</span> Добавление в корзину не резервирует ресурсы. Актуальная цена и наличие проверяются в момент оплаты.
                    </p>
                </div>
            </div>

            @if($cartItems->count() > 0)
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 sticky top-6">
                <h3 class="text-lg font-bold text-white mb-4">Итого к оплате</h3>
                <div class="flex justify-between text-2xl font-bold text-[#a6cb40] font-mono mb-6">
                    <span>Сумма:</span>
                    <span>{{ number_format($cartItems->sum('price'), 2) }} ₽</span>
                </div>
                <p class="text-xs text-gray-500 mb-6 text-center italic">
                    Услуги будут активированы после подтверждения оплаты по каждой позиции отдельно.
                </p>
                <a href="{{ route('dashboard.infrastructure') }}" class="block w-full bg-white/5 hover:bg-white/10 text-white font-bold py-3 rounded-lg transition-colors text-center border border-white/10">
                    Продолжить выбор
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
