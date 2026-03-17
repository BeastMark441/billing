@extends('layouts.landing')

@section('title', 'О компании — NODEUM')
@section('description', 'NODEUM — хостинг‑провайдер: надежная инфраструктура, поддержка 24/7, прозрачные цены.')

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-24">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">О компании <span class="text-[#a6cb40]">NODEUM</span></h1>
                <p class="text-xl text-gray-400 mb-6 leading-relaxed">
                    NODEUM — это современный хостинг-провайдер, основанный энтузиастами технологий в 2024 году. Наша миссия — предоставить доступную и надежную инфраструктуру для проектов любого масштаба.
                </p>
                <p class="text-lg text-gray-400 leading-relaxed">
                    Мы верим, что качественный хостинг не должен быть сложным или дорогим. Мы используем только собственное оборудование в Tier-3 дата-центрах и автоматизируем все процессы, чтобы вы могли сосредоточиться на развитии своего проекта.
                </p>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-[#a6cb40]/20 rounded-3xl blur-2xl"></div>
                <img src="https://coreva-normal.trae.ai/api/ide/v1/text_to_image?prompt=Modern+tech+office+team+working+computers+green+plants+neon+lights&image_size=landscape_4_3" alt="Office Team" class="relative rounded-2xl shadow-2xl border border-white/10">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8">
                <div class="text-4xl font-bold text-[#a6cb40] mb-2">99.9%</div>
                <div class="text-gray-400">Uptime SLA</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8">
                <div class="text-4xl font-bold text-[#a6cb40] mb-2">5000+</div>
                <div class="text-gray-400">Активных клиентов</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8">
                <div class="text-4xl font-bold text-[#a6cb40] mb-2">15 мин</div>
                <div class="text-gray-400">Среднее время ответа поддержки</div>
            </div>
        </div>
    </div>
</section>
@endsection
