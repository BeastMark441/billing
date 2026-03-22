<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Дашборд</h2>
                <p class="text-sm text-gray-400">Сводка платежей, уведомлений и состояния услуг.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('dashboard.billing') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-md text-sm font-medium transition-colors">Финансы</a>
                <a href="{{ route('dashboard.security') }}" class="px-4 py-2 bg-[#a6cb40] hover:bg-[#8eb330] text-black rounded-md text-sm font-medium transition-colors">Безопасность</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
                <div class="text-xs text-gray-400">Баланс</div>
                <div class="mt-2 text-2xl font-bold text-white">{{ number_format((float) $user->balance, 2, '.', ' ') }} ₽</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
                <div class="text-xs text-gray-400">Активные услуги</div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $user->orders()->whereIn('status', ['active', 'suspended', 'provisioning'])->count() }}</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
                <div class="text-xs text-gray-400">Открытые тикеты</div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $user->tickets()->whereIn('status', ['open', 'in_progress'])->count() }}</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-xl p-5">
                <div class="text-xs text-gray-400">Активные заказы</div>
                <div class="mt-2 text-2xl font-bold text-white">{{ $user->orders()->whereIn('status', ['active', 'suspended', 'provisioning'])->count() }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-[#050508] border border-white/10 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white">Последняя активность</h3>
                    <a href="{{ route('dashboard.logs') }}" class="text-sm text-[#a6cb40] hover:underline">Логи</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentActivity as $row)
                        <div class="p-4 bg-white/5 rounded-lg">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm text-white">{{ \App\Support\AuditPresenter::title($row) }}</div>
                                <div class="text-xs text-gray-500">{{ $row->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                            @php($subtitle = \App\Support\AuditPresenter::subtitle($row))
                            @if($subtitle)
                                <div class="mt-1 text-xs text-gray-400">{{ $subtitle }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Нет событий.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">Быстрые действия</h3>
                <div class="space-y-3">
                    <a href="{{ route('dashboard.infrastructure') }}" class="block px-4 py-3 bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 text-white text-sm transition-colors">Оформить услугу</a>
                    <a href="{{ route('dashboard.billing') }}" class="block px-4 py-3 bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 text-white text-sm transition-colors">Пополнить баланс</a>
                    <a href="{{ route('dashboard.tickets.create') }}" class="block px-4 py-3 bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 text-white text-sm transition-colors">Создать тикет</a>
                    <a href="{{ route('dashboard.security') }}" class="block px-4 py-3 bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 text-white text-sm transition-colors">Настроить уведомления</a>
                </div>
            </div>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">Последние заказы</h3>
                <a href="{{ route('orders.index') }}" class="text-sm text-[#a6cb40] hover:underline">Все заказы</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-white/5">
                            <th class="px-4 py-3 font-medium">Заказ</th>
                            <th class="px-4 py-3 font-medium">Услуга</th>
                            <th class="px-4 py-3 font-medium">Статус</th>
                            <th class="px-4 py-3 font-medium">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($recentOrders as $order)
                            <tr class="text-sm">
                                <td class="px-4 py-3">
                                    <a href="{{ route('orders.show', $order) }}" class="text-white hover:underline">#{{ $order->id }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-300">{{ $order->service->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-300">
                                    <span class="{{ $order->status_color }}">{{ $order->status_label }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">Заказов пока нет.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
