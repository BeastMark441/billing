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
                <x-input-label for="surname" :value="__('Фамилия')" class="text-gray-300" />
                <x-text-input id="surname" name="surname" type="text" class="mt-1 block w-full bg-[#0a0a0f] border-white/10 text-white focus:border-[#a6cb40] focus:ring-[#a6cb40]" :value="old('surname', $user->surname)" placeholder="Иванов" autofocus autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('surname')" />
            </div>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Имя')" class="text-gray-300" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-[#0a0a0f] border-white/10 text-white focus:border-[#a6cb40] focus:ring-[#a6cb40]" :value="old('name', $user->name)" required placeholder="Иван" autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <!-- Patronymic -->
            <div>
                <x-input-label for="patronymic" :value="__('Отчество')" class="text-gray-300" />
                <x-text-input id="patronymic" name="patronymic" type="text" class="mt-1 block w-full bg-[#0a0a0f] border-white/10 text-white focus:border-[#a6cb40] focus:ring-[#a6cb40]" :value="old('patronymic', $user->patronymic)" placeholder="Иванович" autocomplete="additional-name" />
                <x-input-error class="mt-2" :messages="$errors->get('patronymic')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-[#0a0a0f] border-white/10 text-white focus:border-[#a6cb40] focus:ring-[#a6cb40]" :value="old('email', $user->email)" required placeholder="email@example.com" autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-200">
                        {{ __('Ваш адрес электронной почты не подтвержден.') }}

                        <button form="send-verification" class="underline text-sm text-gray-400 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#a6cb40]">
                            {{ __('Нажмите здесь, чтобы повторно отправить письмо с подтверждением.') }}
                        </button>
                    </p>

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
