<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Создать тикет</h1>
        <a href="{{ route('admin.tickets.index') }}" class="text-gray-400 hover:text-white transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-8 max-w-4xl">
        <form action="{{ route('admin.tickets.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Selection -->
                <div class="col-span-full">
                    <label for="user_id" class="block text-sm font-medium text-gray-400 mb-2">Пользователь (ID или Email)</label>
                    <input type="text" name="user_id" id="user_id" value="{{ old('user_id') }}" required placeholder="Введите ID пользователя..."
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('user_id') border-red-500 @enderror">
                    @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Тикет будет создан от имени этого пользователя.</p>
                </div>

                <!-- Subject -->
                <div class="col-span-full">
                    <label for="subject" class="block text-sm font-medium text-gray-400 mb-2">Тема обращения</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('subject') border-red-500 @enderror">
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-400 mb-2">Приоритет</label>
                    <select name="priority" id="priority" required
                            class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Низкий</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }} selected>Средний</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Высокий</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Статус</label>
                    <select name="status" id="status" required
                            class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                        <option value="open" {{ old('status') === 'open' ? 'selected' : '' }} selected>Открыт</option>
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>В ожидании</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Закрыт</option>
                    </select>
                </div>

                <!-- Message -->
                <div class="col-span-full">
                    <label for="message" class="block text-sm font-medium text-gray-400 mb-2">Текст сообщения</label>
                    <textarea name="message" id="message" rows="6" required
                              class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-white/5">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                    Создать тикет
                </button>
                <a href="{{ route('admin.tickets.index') }}" class="text-gray-400 hover:text-white transition-colors">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
