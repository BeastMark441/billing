<x-guest-layout>
    <div class="bg-white text-[#0a0a0f] rounded-3xl p-8 shadow-2xl">
        <form method="POST" action="{{ route('register') }}" x-data="{ email: '{{ old('email') }}', password: '', show: false }">
            @csrf

            <input type="hidden" name="name" :value="(email || '').split('@')[0] || 'Пользователь'" />
            <input type="hidden" name="password_confirmation" :value="password" />

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Электронная почта</label>
                <input id="email" name="email" type="email" required autocomplete="username" x-model="email" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-[#0a0a0f] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#a6cb40]/30 focus:border-[#a6cb40]" placeholder="email@example.com" />
                @error('email')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-5">
                <div class="flex items-center justify-between gap-3">
                    <label for="password" class="block text-sm font-medium text-gray-700">Придумайте пароль</label>
                    <button type="button" class="text-sm font-medium text-gray-600 hover:text-[#0a0a0f] underline" @click="password = (Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2)).slice(0, 14)">Сгенерировать</button>
                </div>
                <div class="relative mt-2">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password" x-model="password" class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-sm text-[#0a0a0f] placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#a6cb40]/30 focus:border-[#a6cb40]" />
                    <button type="button" class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-gray-700" @click="show = !show" aria-label="Показать пароль">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
                @error('password')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-5 flex items-start gap-3">
                <input id="newsletter" name="newsletter_consent" type="checkbox" class="mt-1 h-4 w-4 rounded border-gray-300 text-[#0a0a0f] focus:ring-[#a6cb40]/30" />
                <label for="newsletter" class="text-sm text-gray-700">Я подтверждаю свое согласие на получение новостных рассылок</label>
            </div>

            <button type="submit" class="mt-6 w-full rounded-xl bg-[#0e2235] hover:bg-[#0b1c2d] text-white font-bold py-3.5 transition-colors">Создать аккаунт</button>

            <div class="mt-4 text-xs text-gray-600 leading-relaxed">
                Регистрируясь, вы соглашаетесь на обработку персональных данных в соответствии с
                <a href="{{ route('legal.doc', 'privacy') }}" class="text-gray-800 underline hover:text-black">политикой</a>.
            </div>

            <div class="mt-6 text-center text-sm text-gray-700">
                Есть аккаунт?
                <a href="{{ route('login') }}" class="font-bold text-[#0e2235] hover:underline">Войти</a>
            </div>
        </form>
    </div>
</x-guest-layout>

