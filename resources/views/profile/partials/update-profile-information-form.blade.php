<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Информация о профиле') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Обновите информацию профиля вашей учетной записи и адрес электронной почты.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6 max-w-xl">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Surname -->
            <div>
                <x-input-label for="surname" :value="__('Фамилия')" />
                <x-text-input id="surname" name="surname" type="text" :value="old('surname', $user->surname)" placeholder="Иванов" autofocus autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('surname')" />
            </div>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Имя')" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required placeholder="Иван" autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <!-- Patronymic -->
            <div>
                <x-input-label for="patronymic" :value="__('Отчество')" />
                <x-text-input id="patronymic" name="patronymic" type="text" :value="old('patronymic', $user->patronymic)" placeholder="Иванович" autocomplete="additional-name" />
                <x-input-error class="mt-2" :messages="$errors->get('patronymic')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required placeholder="email@example.com" autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                    <p class="text-sm text-yellow-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ __('Ваш адрес электронной почты не подтвержден.') }}
                    </p>

                    <button form="send-verification" class="mt-2 text-sm text-gray-300 hover:text-white underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#a6cb40] rounded-md">
                        {{ __('Нажмите здесь, чтобы повторно отправить письмо с подтверждением.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            {{ __('Новая ссылка для подтверждения была отправлена на ваш адрес электронной почты.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] focus:ring-[#a6cb40]">{{ __('Сохранить') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-400"
                >{{ __('Сохранено.') }}</p>
            @endif
        </div>
    </form>
</section>
