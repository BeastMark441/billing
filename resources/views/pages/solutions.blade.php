@extends('layouts.landing')

@section('title', 'Готовые решения — NODEUM')
@section('description', 'Готовые решения NODEUM для бизнеса, игр, корпоративных клиентов и медиа. Быстрый запуск и масштабирование.')

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Готовые <span class="text-[#a6cb40]">решения</span></h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Мы разработали специализированные решения для различных отраслей бизнеса.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 flex flex-col md:flex-row gap-6 items-center">
                <img src="https://coreva-normal.trae.ai/api/ide/v1/text_to_image?prompt=E-commerce+business+concept+shopping+cart+digital+interface+futuristic+green+accent&image_size=square" alt="E-commerce" class="w-32 h-32 rounded-xl object-cover">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Для E-commerce</h3>
                    <p class="text-gray-400 mb-4">Высокоскоростной хостинг для интернет-магазинов. Оптимизировано для Magento, WooCommerce, Bitrix.</p>
                    <a href="{{ route('products') }}" class="text-[#a6cb40] font-bold hover:underline">Подробнее</a>
                </div>
            </div>

            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 flex flex-col md:flex-row gap-6 items-center">
                <img src="https://coreva-normal.trae.ai/api/ide/v1/text_to_image?prompt=Game+development+code+screen+controller+futuristic+green+accent&image_size=square" alt="Game Dev" class="w-32 h-32 rounded-xl object-cover">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Для разработчиков игр</h3>
                    <p class="text-gray-400 mb-4">Инфраструктура для размещения игровых серверов, баз данных и матчмейкинга.</p>
                    <a href="{{ route('products') }}" class="text-[#a6cb40] font-bold hover:underline">Подробнее</a>
                </div>
            </div>

            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 flex flex-col md:flex-row gap-6 items-center">
                <img src="https://coreva-normal.trae.ai/api/ide/v1/text_to_image?prompt=Corporate+office+building+digital+network+futuristic+green+accent&image_size=square" alt="Enterprise" class="w-32 h-32 rounded-xl object-cover">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Корпоративным клиентам</h3>
                    <p class="text-gray-400 mb-4">Частные облака, VPN-решения и выделенная инфраструктура с SLA 99.99%.</p>
                    <a href="{{ route('products') }}" class="text-[#a6cb40] font-bold hover:underline">Подробнее</a>
                </div>
            </div>

            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 flex flex-col md:flex-row gap-6 items-center">
                <img src="https://coreva-normal.trae.ai/api/ide/v1/text_to_image?prompt=Media+streaming+play+button+digital+waves+futuristic+green+accent&image_size=square" alt="Streaming" class="w-32 h-32 rounded-xl object-cover">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Стриминг и медиа</h3>
                    <p class="text-gray-400 mb-4">Серверы с каналом 10Gbps+ для трансляции видео и хранения большого объема данных.</p>
                    <a href="{{ route('products') }}" class="text-[#a6cb40] font-bold hover:underline">Подробнее</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
