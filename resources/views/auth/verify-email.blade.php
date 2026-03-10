<x-guest-layout>
    <div class="mb-4 text-sm text-gray-400">
        Спасибо за регистрацию! Прежде чем начать, не могли бы вы подтвердить свой адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам? Если вы не получили письмо, мы с радостью отправим вам другое.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-[#a6cb40]">
            Новая ссылка для подтверждения была отправлена на адрес электронной почты, указанный при регистрации.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <button class="inline-flex items-center px-4 py-2 bg-[#a6cb40] border border-transparent rounded-md font-bold text-xs text-[#0a0a0f] uppercase tracking-widest hover:bg-[#8eb330] focus:bg-[#8eb330] active:bg-[#8eb330] focus:outline-none focus:ring-2 focus:ring-[#a6cb40] focus:ring-offset-2 transition ease-in-out duration-150">
                    Отправить письмо повторно
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-400 hover:text-[#a6cb40] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#a6cb40]">
                Выйти
            </button>
        </form>
    </div>
</x-guest-layout>
