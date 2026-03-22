<x-app-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Мои заказы</h2>
        <p class="text-gray-400">Управление вашими услугами и серверами</p>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">ID</th>
                        <th class="px-6 py-4 font-medium">Услуга</th>
                        <th class="px-6 py-4 font-medium">Статус</th>
                        <th class="px-6 py-4 font-medium">IP адрес</th>
                        <th class="px-6 py-4 font-medium">Цена</th>
                        <th class="px-6 py-4 font-medium">Дата создания</th>
                        <th class="px-6 py-4 font-medium text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">{{ $order->service->name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->service->category->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color }} bg-white/5">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-300 font-mono text-sm">
                            @if($order->server_ip)
                                {{ $order->server_ip }}:{{ $order->server_port }}
                            @else
                                <span class="text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-white font-medium">
                            {{ number_format($order->price, 2) }} ₽
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            {{ $order->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                                Подробнее
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p class="mb-2">У вас пока нет активных заказов</p>
                                <a href="{{ route('dashboard.infrastructure') }}" class="text-blue-400 hover:text-blue-300">Перейти в каталог услуг &rarr;</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
