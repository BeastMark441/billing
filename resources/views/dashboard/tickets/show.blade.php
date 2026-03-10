<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white mb-1">Тикет #{{ $ticket->id }}</h2>
                <div class="flex items-center gap-2 text-sm text-gray-400">
                    <span class="text-white">{{ $ticket->subject }}</span>
                    <span>•</span>
                    <span>{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
            <a href="{{ route('dashboard.tickets.index') }}" class="text-gray-400 hover:text-white transition-colors">
                &larr; Назад к списку
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chat Section -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Messages -->
                @foreach($ticket->messages as $message)
                    <div class="bg-[#050508] border {{ $message->user->role === 'admin' ? 'border-[#a6cb40]/20 bg-[#a6cb40]/5' : 'border-white/10' }} rounded-xl p-6 group">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full {{ $message->user->role === 'admin' ? 'bg-[#a6cb40] text-[#0a0a0f]' : 'bg-white/10 text-white' }} flex items-center justify-center font-bold flex-shrink-0">
                                {{ mb_strtoupper(mb_substr($message->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-white">
                                        {{ $message->user->name }}
                                        @if($message->user->role === 'admin')
                                            <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] bg-[#a6cb40] text-[#0a0a0f] uppercase tracking-wider font-bold">Support</span>
                                        @endif
                                    </span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-500">{{ $message->created_at->format('d.m.Y H:i') }}</span>
                                        @if($message->user_id === Auth::id())
                                            <div class="hidden group-hover:flex items-center gap-2">
                                                <button onclick="alert('Редактирование пока не реализовано в UI')" class="text-gray-500 hover:text-white transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <form action="{{ route('dashboard.tickets.message.destroy', $message) }}" method="POST" onsubmit="return confirm('Удалить сообщение?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-500 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-gray-300 whitespace-pre-wrap">{{ $message->message }}</div>
                                
                                @if($message->attachments->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-white/5 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="flex items-center gap-3 p-2 rounded bg-white/5 hover:bg-white/10 transition-colors">
                                                <div class="text-gray-400">
                                                    @if(str_starts_with($attachment->file_type, 'image/'))
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm text-white truncate">{{ $attachment->file_name }}</div>
                                                    <div class="text-xs text-gray-500">{{ number_format($attachment->file_size / 1024, 2) }} KB</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Reply Form -->
                @if($ticket->status !== 'closed')
                <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Написать ответ</h3>
                    <form action="{{ route('dashboard.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <textarea name="message" rows="4" placeholder="Введите ваш ответ..." class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#a6cb40] transition-colors" required></textarea>
                        
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-400 mb-2">Прикрепить файлы</label>
                            <input type="file" name="attachments[]" id="attachments" multiple class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#a6cb40]/10 file:text-[#a6cb40] hover:file:bg-[#a6cb40]/20 transition-colors cursor-pointer">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] rounded-lg transition-colors font-bold">
                                Отправить
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="bg-gray-500/10 border border-white/10 rounded-xl p-6 text-center text-gray-400">
                    Этот тикет закрыт. Вы не можете отправлять новые сообщения.
                </div>
                @endif
            </div>

            <!-- Sidebar Info -->
            <div class="lg:col-span-1 space-y-6">
                @if($ticket->order)
                <div class="bg-[#050508] border border-blue-500/20 rounded-xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-4">Связанная услуга</h3>
                    
                    <div class="space-y-3 relative z-10">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Услуга</div>
                            <div class="text-white font-medium">{{ $ticket->order->service->name }}</div>
                        </div>
                        
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">IP Адрес</div>
                            <div class="font-mono text-sm text-gray-300">{{ $ticket->order->server_ip ?? 'Ожидает' }}:{{ $ticket->order->server_port ?? '' }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Статус услуги</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                {{ $ticket->order->status === 'active' ? 'bg-green-500/10 text-green-500' : 
                                   ($ticket->order->status === 'failed' ? 'bg-red-500/10 text-red-500' : 'bg-gray-500/10 text-gray-500') }}">
                                {{ $ticket->order->status }}
                            </span>
                        </div>
                        
                        <div class="pt-3 border-t border-white/10">
                            <a href="{{ route('orders.show', $ticket->order) }}" class="block w-full text-center px-4 py-2 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-lg transition-colors font-medium text-sm">
                                Управление услугой
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Информация</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 mb-1 uppercase">Статус</div>
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $statusColors[$ticket->status] ?? 'bg-gray-500/10 text-gray-500' }}">
                                {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                            </span>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 mb-1 uppercase">Приоритет</div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase {{ $ticket->priority === 'high' ? 'bg-red-500/10 text-red-500' : ($ticket->priority === 'medium' ? 'bg-yellow-500/10 text-yellow-500' : 'bg-blue-500/10 text-blue-500') }}">
                                {{ $ticket->priority }}
                            </span>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 mb-1 uppercase">Последнее обновление</div>
                            <div class="text-white text-sm">{{ $ticket->updated_at->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
