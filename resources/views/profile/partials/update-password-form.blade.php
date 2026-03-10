<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Обновление пароля') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Убедитесь, что ваша учетная запись использует длинный случайный пароль, чтобы оставаться в безопасности.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6 max-w-xl">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Текущий пароль')" class="text-gray-300" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-[#0a0a0f] border-white/10 text-white focus:border-[#a6cb40] focus:ring-[#a6cb40]" placeholder="Введите текущий пароль" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Новый пароль')" class="text-gray-300" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-[#0a0a0f] border-white/10 text-white focus:border-[#a6cb40] focus:ring-[#a6cb40]" placeholder="Введите новый пароль" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Подтверждение пароля')" class="text-gray-300" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-[#0a0a0f] border-white/10 text-white focus:border-[#a6cb40] focus:ring-[#a6cb40]" placeholder="Повторите новый пароль" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] focus:ring-[#a6cb40]">{{ __('Сохранить') }}</x-primary-button>
        </div>
    </form>
</section>
