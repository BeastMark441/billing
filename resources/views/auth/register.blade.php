<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <h2 class="text-2xl font-bold text-white mb-6 text-center">Регистрация</h2>

        <!-- Name -->
        <div>
            <label for="name" class="block font-medium text-sm text-gray-300">Имя</label>
            <input id="name" class="block mt-1 w-full bg-[#050508] border border-white/10 rounded-md shadow-sm focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-white px-3 py-2" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block font-medium text-sm text-gray-300">Email</label>
            <input id="email" class="block mt-1 w-full bg-[#050508] border border-white/10 rounded-md shadow-sm focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-white px-3 py-2" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-300">Пароль</label>
            <input id="password" class="block mt-1 w-full bg-[#050508] border border-white/10 rounded-md shadow-sm focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-white px-3 py-2" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-300">Подтвердите пароль</label>
            <input id="password_confirmation" class="block mt-1 w-full bg-[#050508] border border-white/10 rounded-md shadow-sm focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-white px-3 py-2" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-400 hover:text-[#a6cb40] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#a6cb40]" href="{{ route('login') }}">
                Уже зарегистрированы?
            </a>

            <button class="ms-4 inline-flex items-center px-4 py-2 bg-[#a6cb40] border border-transparent rounded-md font-bold text-xs text-[#0a0a0f] uppercase tracking-widest hover:bg-[#8eb330] focus:bg-[#8eb330] active:bg-[#8eb330] focus:outline-none focus:ring-2 focus:ring-[#a6cb40] focus:ring-offset-2 transition ease-in-out duration-150">
                Создать аккаунт
            </button>
        </div>
    </form>
</x-guest-layout>
