<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Создание заказа</h1>
            <p class="text-gray-400">Ручное создание заказа и сервера для пользователя</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-white transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.orders.store') }}">
            @csrf

            <!-- User -->
            <div class="mb-6">
                <label for="user_id" class="block text-sm font-medium text-gray-300 mb-2">Пользователь</label>
                <select name="user_id" id="user_id" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                    <option value="">Выберите пользователя...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->email }} ({{ $user->name }}) - Баланс: {{ number_format($user->balance, 2) }} ₽
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Service -->
            <div class="mb-6">
                <label for="infrastructure_service_id" class="block text-sm font-medium text-gray-300 mb-2">Услуга</label>
                <select name="infrastructure_service_id" id="infrastructure_service_id" class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                    <option value="">Выберите тариф...</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('infrastructure_service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} - {{ number_format($service->price, 2) }} ₽
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Expires At -->
            <div class="mb-6">
                <label for="expires_at" class="block text-sm font-medium text-gray-300 mb-2">Дата истечения (опционально)</label>
                <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', now()->addMonth()->format('Y-m-d')) }}" 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">По умолчанию: +30 дней от сегодня</p>
            </div>

            <!-- Payment Method -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-300 mb-2">Оплата</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/5 cursor-pointer hover:bg-white/10 transition-colors">
                        <input type="radio" name="payment_method" value="balance" class="text-blue-600 focus:ring-blue-500 bg-[#0a0a0f] border-white/10" {{ old('payment_method', 'balance') == 'balance' ? 'checked' : '' }}>
                        <div>
                            <div class="text-white font-medium">Списать с баланса пользователя</div>
                            <div class="text-xs text-gray-500">Средства будут списаны автоматически</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/5 cursor-pointer hover:bg-white/10 transition-colors">
                        <input type="radio" name="payment_method" value="free" class="text-blue-600 focus:ring-blue-500 bg-[#0a0a0f] border-white/10" {{ old('payment_method') == 'free' ? 'checked' : '' }}>
                        <div>
                            <div class="text-white font-medium">Бесплатно (Подарок/Тест)</div>
                            <div class="text-xs text-gray-500">Списания не будет, заказ сразу станет активным</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-white/10">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Создать заказ
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>