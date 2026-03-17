@extends('layouts.landing')

@section('title', $docTitle.' — NODEUM')
@section('description', $docTitle.' NODEUM. Ознакомьтесь с условиями и правилами.')

@section('content')
<section class="py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="text-sm text-gray-400">
                <a href="{{ route('legal') }}" class="hover:underline">Юридические документы</a>
                <span class="mx-2">/</span>
                <span class="text-gray-300">{{ $docTitle }}</span>
            </div>
            <h1 class="mt-3 text-4xl font-bold text-white">{{ $docTitle }}</h1>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 text-gray-200 leading-relaxed space-y-4">
            <p class="text-sm text-gray-400">Документ подготовлен как шаблон. Заполните фактическими юридическими данными и актуальными условиями.</p>
            <h2 class="text-lg font-bold text-white">1. Общие положения</h2>
            <p>Настоящий документ регулирует порядок использования сервисов NODEUM и условия оказания услуг.</p>
            <h2 class="text-lg font-bold text-white">2. Права и обязанности</h2>
            <p>Пользователь обязуется соблюдать правила сервиса, а также не использовать услуги для противоправных действий.</p>
            <h2 class="text-lg font-bold text-white">3. Персональные данные</h2>
            <p>Обработка персональных данных осуществляется в соответствии с применимым законодательством и политиками компании.</p>
            <h2 class="text-lg font-bold text-white">4. Cookies</h2>
            <p>Сайт использует cookies для работы личного кабинета, аналитики и улучшения качества сервиса.</p>
            <h2 class="text-lg font-bold text-white">5. Контакты</h2>
            <p>Связаться с нами можно через страницу <a href="{{ route('contacts') }}" class="text-[#a6cb40] hover:underline">Контакты</a>.</p>
        </div>
    </div>
</section>
@endsection

