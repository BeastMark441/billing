<x-admin-layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Управление заказами</h1>
            <p class="text-gray-400">Список всех заказов и серверов пользователей</p>
        </div>
        
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-2">
            <a href="{{ route('admin.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Создать заказ
            </a>
            <select name="status" onchange="this.form.submit()" 
                    class="bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Все статусы</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Приостановленные</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ожидают оплаты</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Ошибка</option>
            </select>
            
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск (ID, UUID, Email)..." 
                   class="bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 w-64">
        </form>
    </div>

    <!-- Failed Orders Alert -->
    @if($orders->where('status', 'failed')->count() > 0)
    <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-bold text-red-400 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Проблемные заказы
        </h3>
        <p class="text-gray-400 text-sm mb-4">Обнаружены заказы со статусом "Ошибка". Требуется ручное вмешательство.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-red-500/20">
                        <th class="pb-2">ID</th>
                        <th class="pb-2">Пользователь</th>
                        <th class="pb-2">Услуга</th>
                        <th class="pb-2 text-right">Действие</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-500/10">
                    @foreach($orders->where('status', 'failed') as $failedOrder)
                    <tr>
                        <td class="py-3 text-red-300">#{{ $failedOrder->id }}</td>
                        <td class="py-3 text-white">{{ $failedOrder->user->email }}</td>
                        <td class="py-3 text-gray-300">{{ $failedOrder->service->name }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('admin.orders.show', $failedOrder) }}" class="text-red-400 hover:text-red-300 text-sm font-medium">Исправить &rarr;</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">ID</th>
                        <th class="px-6 py-4 font-medium">Пользователь</th>
                        <th class="px-6 py-4 font-medium">Услуга</th>
                        <th class="px-6 py-4 font-medium">Статус</th>
                        <th class="px-6 py-4 font-medium">Сервер</th>
                        <th class="px-6 py-4 font-medium">Истекает</th>
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
                        <td class="px-6 py-4">
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->server_ip)
                                <div class="text-xs font-mono text-gray-300">{{ $order->server_ip }}:{{ $order->server_port }}</div>
                                <div class="text-[10px] text-gray-500 break-all">{{ $order->pterodactyl_server_identifier }}</div>
                            @else
                                <span class="text-gray-600 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $order->expires_at ? $order->expires_at->format('d.m.Y') : 'Бессрочно' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                                Управление
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Заказы не найдены
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
</x-admin-layout>
