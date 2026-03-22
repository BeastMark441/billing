<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Тикеты поддержки</h1>
            <p class="text-gray-400">Управление обращениями пользователей</p>
        </div>
        <a href="{{ route('admin.tickets.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
            Создать тикет
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">ID</th>
                        <th class="px-6 py-4 font-medium">Пользователь</th>
                        <th class="px-6 py-4 font-medium">Тема</th>
                        <th class="px-6 py-4 font-medium">Статус</th>
                        <th class="px-6 py-4 font-medium">Приоритет</th>
                        <th class="px-6 py-4 font-medium">Дата создания</th>
                        <th class="px-6 py-4 font-medium text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                            #{{ $ticket->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white text-xs font-bold overflow-hidden">
                                    {{ mb_strtoupper(mb_substr($ticket->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-white text-sm">{{ $ticket->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-white font-medium">
                            {{ $ticket->subject }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'open' => 'bg-green-500/10 text-green-500',
                                    'pending' => 'bg-yellow-500/10 text-yellow-500',
                                    'closed' => 'bg-gray-500/10 text-gray-500',
                                ];
                                $statusLabels = [
                                    'open' => 'Открыт',
                                    'pending' => 'В ожидании',
                                    'closed' => 'Закрыт',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                                {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $priorityColors = [
                                    'low' => 'text-gray-400',
                                    'medium' => 'text-yellow-500',
                                    'high' => 'text-red-500',
                                ];
                                $priorityLabels = [
                                    'low' => 'Низкий',
                                    'medium' => 'Средний',
                                    'high' => 'Высокий',
                                ];
                            @endphp
                            <span class="text-xs font-medium {{ $priorityColors[$ticket->priority] ?? 'text-gray-400' }}">
                                {{ $priorityLabels[$ticket->priority] ?? ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            {{ $ticket->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition-colors">
                                Просмотр
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Тикетов пока нет
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
