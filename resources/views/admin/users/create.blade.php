<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Создание пользователя</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-8 max-w-4xl">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Surname -->
                <div>
                    <label for="surname" class="block text-sm font-medium text-gray-400 mb-2">Фамилия</label>
                    <input type="text" name="surname" id="surname" value="{{ old('surname') }}" 
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('surname') border-red-500 @enderror">
                    @error('surname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Имя</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Patronymic -->
                <div>
                    <label for="patronymic" class="block text-sm font-medium text-gray-400 mb-2">Отчество (если есть)</label>
                    <input type="text" name="patronymic" id="patronymic" value="{{ old('patronymic') }}" 
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('patronymic') border-red-500 @enderror">
                    @error('patronymic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-400 mb-2">Роль</label>
                    <select name="role" id="role" required
                            class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Пользователь</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Администратор</option>
                        <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Супер-админ</option>
                    </select>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Пароль</label>
                    <input type="password" name="password" id="password" required
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors @error('password') border-red-500 @enderror">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-2">Подтвердите пароль</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full bg-[#050508] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 transition-colors">
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-white/5">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                    Создать пользователя
                </button>
                <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition-colors">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
