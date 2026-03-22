<x-admin-layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">В Корзине</h1>
            <p class="text-gray-400">Список заказов, которые пользователи добавили в корзину</p>
        </div>
        
        <form method="GET" action="{{ route('admin.orders.cart.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск (ID, Email)..." 
                   class="bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 w-64">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Найти</button>
        </form>
    </div>

    <div class="flex border-b border-white/5 mb-8">
        <a href="{{ route('admin.orders.index') }}" class="px-6 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('admin.orders.index') ? 'border-blue-500 text-white' : 'border-transparent text-gray-500 hover:text-white' }}">
            Все заказы
        </a>
        <a href="{{ route('admin.orders.cart.index') }}" class="px-6 py-3 text-sm font-medium border-b-2 {{ request()->routeIs('admin.orders.cart.index') ? 'border-blue-500 text-white' : 'border-transparent text-gray-500 hover:text-white' }}">
            В Корзине
        </a>
    </div>

    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-2xl p-6 mb-8 text-yellow-200 text-sm">
        <p><strong>Примечание:</strong> Заказы в корзине автоматически удаляются через 7 дней бездействия. Вы можете просмотреть их здесь для аналитики заброшенных продаж.</p>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">ID</th>
                        <th class="px-6 py-4 font-medium">Пользователь</th>
                        <th class="px-6 py-4 font-medium">Услуга</th>
                        <th class="px-6 py-4 font-medium">Цена</th>
                        <th class="px-6 py-4 font-medium">Добавлено</th>
                        <th class="px-6 py-4 font-medium">Истекает через</th>
                        <th class="px-6 py-4 font-medium text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">#{{ $order->id }}</td>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">{{ $order->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $order->service->name }}</td>
                        <td class="px-6 py-4 text-white font-bold">{{ number_format($order->price, 2) }} ₽</td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $order->cart_added_at?->format('d.m.Y H:i') ?? $order->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $daysLeft = 7 - ($order->cart_added_at ?? $order->created_at)->diffInDays(now());
                                $roundedDays = ceil($daysLeft);
                            @endphp
                            <span class="{{ $roundedDays <= 1 ? 'text-red-400 font-bold' : 'text-yellow-400' }}">
                                {{ max(0, $roundedDays) }} дн.
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" 
                                    onclick="alert('Взаимодействие с товарами в корзине ограничено. Карточка товара недоступна до момента оплаты.')"
                                    class="text-gray-500 cursor-not-allowed text-sm font-medium">
                                Детали
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Корзина пуста</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-white/5">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
