@extends('layouts.landing')

@section('title', 'Контакты — NODEUM')
@section('description', 'Контакты NODEUM: поддержка, реквизиты и способы связи.')

@section('content')
<section class="py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white">Контакты</h1>
            <p class="mt-3 text-lg text-gray-400">Если у вас есть вопросы по услугам или оплате — напишите нам.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white">Поддержка</h2>
                <div class="mt-4 space-y-2 text-sm text-gray-300">
                    <div class="flex items-center justify-between gap-3 bg-white/5 rounded-lg px-4 py-3">
                        <div class="text-gray-400">Email</div>
                        <div class="text-white">support@nodeum.ru</div>
                    </div>
                    <div class="flex items-center justify-between gap-3 bg-white/5 rounded-lg px-4 py-3">
                        <div class="text-gray-400">Время</div>
                        <div class="text-white">24/7</div>
                    </div>
                </div>
                <div class="mt-5">
                    <a href="{{ route('dashboard.tickets.create') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-colors">Создать тикет</a>
                </div>
            </div>

            <div class="bg-[#050508] border border-white/10 rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white">Реквизиты</h2>
                <p class="mt-3 text-sm text-gray-400">Здесь можно разместить юридическую информацию и реквизиты компании.</p>
                <div class="mt-4 space-y-2 text-sm text-gray-300">
                    <div class="flex items-center justify-between gap-3 bg-white/5 rounded-lg px-4 py-3">
                        <div class="text-gray-400">Наименование</div>
                        <div class="text-white">NODEUM</div>
                    </div>
                    <div class="flex items-center justify-between gap-3 bg-white/5 rounded-lg px-4 py-3">
                        <div class="text-gray-400">ИНН/ОГРН</div>
                        <div class="text-white">—</div>
                    </div>
                    <div class="flex items-center justify-between gap-3 bg-white/5 rounded-lg px-4 py-3">
                        <div class="text-gray-400">Адрес</div>
                        <div class="text-white">—</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

