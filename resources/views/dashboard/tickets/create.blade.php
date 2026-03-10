<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('dashboard.tickets.index') }}" class="text-sm text-gray-400 hover:text-white flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Назад к списку
            </a>
            <h2 class="text-2xl font-bold text-white">Создание тикета</h2>
            <p class="text-sm text-gray-400">Опишите вашу проблему, и мы поможем вам в ближайшее время.</p>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
            <form method="POST" action="{{ route('dashboard.tickets.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Subject -->
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-300 mb-1">Тема обращения</label>
                    <input type="text" name="subject" id="subject" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50" required placeholder="Например: Проблема с VDS">
                    <x-input-error :messages="$errors->get('subject')" class="mt-2 text-red-400" />
                </div>

                <!-- Related Service (Order) -->
                @if($orders->count() > 0)
                <div class="mb-4">
                    <label for="order_id" class="block text-sm font-medium text-gray-300 mb-1">Связанная услуга (опционально)</label>
                    <select name="order_id" id="order_id" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50">
                        <option value="">-- Не выбрано --</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                                [#{{ $order->id }}] {{ $order->service->name }} ({{ $order->server_ip ?? 'Ожидает' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Выберите услугу, если проблема связана с конкретным сервером.</p>
                    <x-input-error :messages="$errors->get('order_id')" class="mt-2 text-red-400" />
                </div>
                @endif

                <!-- Priority -->
                <div class="mb-4">
                    <label for="priority" class="block text-sm font-medium text-gray-300 mb-1">Приоритет</label>
                    <select name="priority" id="priority" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50">
                        <option value="low">Низкий (Вопрос, консультация)</option>
                        <option value="medium" selected>Средний (Проблема в работе)</option>
                        <option value="high">Высокий (Критический сбой, недоступность)</option>
                    </select>
                    <x-input-error :messages="$errors->get('priority')" class="mt-2 text-red-400" />
                </div>

                <!-- Message -->
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-300 mb-1">Сообщение</label>
                    <textarea name="message" id="message" rows="6" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50" required placeholder="Подробно опишите вашу проблему..."></textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2 text-red-400" />
                </div>

                <!-- Attachments -->
                <div class="mb-6">
                    <label for="attachments" class="block text-sm font-medium text-gray-300 mb-1">Вложения (до 100МБ)</label>
                    <input type="file" name="attachments[]" id="attachments" multiple class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#a6cb40]/10 file:text-[#a6cb40] hover:file:bg-[#a6cb40]/20 transition-colors cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Поддерживаемые форматы: изображения, документы, логи, архивы.</p>
                    <x-input-error :messages="$errors->get('attachments.*')" class="mt-2 text-red-400" />
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] px-6 py-2 rounded-lg font-bold transition-all shadow-[0_0_15px_rgba(166,203,64,0.2)] hover:shadow-[0_0_20px_rgba(166,203,64,0.4)]">
                        Отправить тикет
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
