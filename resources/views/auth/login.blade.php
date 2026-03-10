<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <h2 class="text-2xl font-bold text-white mb-6 text-center">Вход в систему</h2>

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-300">Email</label>
            <input id="email" class="block mt-1 w-full bg-[#050508] border border-white/10 rounded-md shadow-sm focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-white px-3 py-2" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-300">Пароль</label>
            <input id="password" class="block mt-1 w-full bg-[#050508] border border-white/10 rounded-md shadow-sm focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-white px-3 py-2" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-white/10 bg-[#050508] text-[#a6cb40] shadow-sm focus:ring-[#a6cb40]" name="remember">
                <span class="ms-2 text-sm text-gray-400">Запомнить меня</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-400 hover:text-[#a6cb40] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#a6cb40]" href="{{ route('password.request') }}">
                    Забыли пароль?
                </a>
            @endif

            <button class="ms-3 inline-flex items-center px-4 py-2 bg-[#a6cb40] border border-transparent rounded-md font-bold text-xs text-[#0a0a0f] uppercase tracking-widest hover:bg-[#8eb330] focus:bg-[#8eb330] active:bg-[#8eb330] focus:outline-none focus:ring-2 focus:ring-[#a6cb40] focus:ring-offset-2 transition ease-in-out duration-150">
                Войти
            </button>
        </div>
        
        <div class="mt-6 text-center">
             <span class="text-sm text-gray-400">Нет аккаунта?</span>
             <a href="{{ route('register') }}" class="text-sm font-bold text-[#a6cb40] hover:underline ml-1">Зарегистрироваться</a>
        </div>
    </form>
</x-guest-layout>
