<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white">Тикеты поддержки</h2>
                <p class="text-sm text-gray-400">Управление вашими обращениями в службу поддержки.</p>
            </div>
            <a href="{{ route('dashboard.tickets.create') }}" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] px-4 py-2 rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Новый тикет
            </a>
        </div>

        @if(session('success'))
        <div class="bg-[#a6cb40]/10 border border-[#a6cb40]/20 text-[#a6cb40] px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif

        <!-- Filters -->
        <div class="flex gap-2">
            <a href="{{ route('dashboard.tickets.index', ['status' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-[#a6cb40] text-[#0a0a0f]' : 'bg-white/5 text-gray-400 hover:text-white' }}">
                Все
            </a>
            <a href="{{ route('dashboard.tickets.index', ['status' => 'open']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'open' ? 'bg-[#a6cb40] text-[#0a0a0f]' : 'bg-white/5 text-gray-400 hover:text-white' }}">
                Открытые
            </a>
            <a href="{{ route('dashboard.tickets.index', ['status' => 'closed']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'closed' ? 'bg-[#a6cb40] text-[#0a0a0f]' : 'bg-white/5 text-gray-400 hover:text-white' }}">
                Закрытые
            </a>
        </div>

        <!-- Tickets List -->
        <div class="space-y-4">
            @forelse ($tickets as $ticket)
            <a href="{{ route('dashboard.tickets.show', $ticket) }}" class="block bg-[#050508] border border-white/10 rounded-xl p-4 hover:border-[#a6cb40]/50 transition-colors cursor-pointer group">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
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
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase {{ $statusColors[$ticket->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                                {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                            </span>
                            <span class="text-xs text-gray-500">#{{ $ticket->id }}</span>
                            <span class="text-xs text-gray-500">• {{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-white group-hover:text-[#a6cb40] transition-colors">{{ $ticket->subject }}</h3>
                        <p class="text-sm text-gray-400 mt-1 line-clamp-1">{{ $ticket->messages->last()->message ?? $ticket->message }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase {{ $ticket->priority === 'high' ? 'bg-red-500/10 text-red-500' : ($ticket->priority === 'medium' ? 'bg-yellow-500/10 text-yellow-500' : 'bg-blue-500/10 text-blue-500') }}">
                            {{ $ticket->priority }} priority
                        </span>
                        @if($ticket->messages->count() > 0)
                            <span class="text-xs text-gray-500 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                {{ $ticket->messages->count() }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-12 bg-[#050508] border border-white/10 rounded-xl">
                <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                <h3 class="text-lg font-medium text-white mb-2">Нет тикетов</h3>
                <p class="text-gray-500 text-sm mb-6">У вас пока нет обращений в службу поддержки.</p>
                <a href="{{ route('dashboard.tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-[#a6cb40] border border-transparent rounded-md font-bold text-xs text-[#0a0a0f] uppercase tracking-widest hover:bg-[#8eb330] transition ease-in-out duration-150">
                    Создать тикет
                </a>
            </div>
            @endforelse

            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
