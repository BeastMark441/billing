@extends('layouts.landing')

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Наши <span class="text-[#a6cb40]">продукты</span></h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Мы предлагаем широкий спектр хостинг-решений для любых задач. От простых сайтов до сложных игровых кластеров.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- VDS -->
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 hover:border-[#a6cb40]/50 transition-all">
                <div class="w-14 h-14 bg-[#a6cb40]/10 rounded-xl flex items-center justify-center text-[#a6cb40] mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">VDS / VPS</h3>
                <p class="text-gray-400 mb-6">Виртуальные серверы на базе KVM с NVMe дисками. Полная изоляция ресурсов.</p>
                <ul class="space-y-3 mb-8 text-sm text-gray-300">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> До 64 vCPU</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> NVMe накопители</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Защита от DDoS</li>
                </ul>
                <a href="#" class="block w-full text-center py-3 border border-white/20 rounded-lg hover:bg-[#a6cb40] hover:text-[#0a0a0f] hover:border-[#a6cb40] transition-colors font-bold">Выбрать тариф</a>
            </div>

            <!-- Game Hosting -->
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 hover:border-[#a6cb40]/50 transition-all">
                <div class="w-14 h-14 bg-[#a6cb40]/10 rounded-xl flex items-center justify-center text-[#a6cb40] mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">Игровой хостинг</h3>
                <p class="text-gray-400 mb-6">Серверы для популярных игр с удобной панелью управления.</p>
                <ul class="space-y-3 mb-8 text-sm text-gray-300">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Minecraft, CS2, Rust</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Низкий пинг</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Автоустановка модов</li>
                </ul>
                <a href="#" class="block w-full text-center py-3 border border-white/20 rounded-lg hover:bg-[#a6cb40] hover:text-[#0a0a0f] hover:border-[#a6cb40] transition-colors font-bold">Выбрать игру</a>
            </div>

            <!-- Dedicated -->
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 hover:border-[#a6cb40]/50 transition-all">
                <div class="w-14 h-14 bg-[#a6cb40]/10 rounded-xl flex items-center justify-center text-[#a6cb40] mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">Выделенные серверы</h3>
                <p class="text-gray-400 mb-6">Мощные физические серверы для высоконагруженных проектов.</p>
                <ul class="space-y-3 mb-8 text-sm text-gray-300">
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Intel & AMD EPYC</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> До 1TB RAM</li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Индивидуальная сборка</li>
                </ul>
                <a href="#" class="block w-full text-center py-3 border border-white/20 rounded-lg hover:bg-[#a6cb40] hover:text-[#0a0a0f] hover:border-[#a6cb40] transition-colors font-bold">Конфигуратор</a>
            </div>
        </div>
    </div>
</section>
@endsection
