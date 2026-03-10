<x-guest-layout>
    <div class="mb-4 text-sm text-gray-400">
        Забыли пароль? Без проблем. Просто сообщите нам свой адрес электронной почты, и мы отправим вам ссылку для сброса пароля, которая позволит вам выбрать новый.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-300">Email</label>
            <input id="email" class="block mt-1 w-full bg-[#050508] border border-white/10 rounded-md shadow-sm focus:border-[#a6cb40] focus:ring focus:ring-[#a6cb40] focus:ring-opacity-50 text-white px-3 py-2" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button class="inline-flex items-center px-4 py-2 bg-[#a6cb40] border border-transparent rounded-md font-bold text-xs text-[#0a0a0f] uppercase tracking-widest hover:bg-[#8eb330] focus:bg-[#8eb330] active:bg-[#8eb330] focus:outline-none focus:ring-2 focus:ring-[#a6cb40] focus:ring-offset-2 transition ease-in-out duration-150">
                Отправить ссылку
            </button>
        </div>
    </form>
</x-guest-layout>
