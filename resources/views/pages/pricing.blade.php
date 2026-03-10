@extends('layouts.landing')

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Прозрачные <span class="text-[#a6cb40]">цены</span></h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Никаких скрытых платежей. Вы платите только за те ресурсы, которые используете.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Start -->
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 hover:border-[#a6cb40]/30 transition-all">
                <h3 class="text-xl font-bold mb-2">Start</h3>
                <div class="text-4xl font-bold text-white mb-6">450 ₽ <span class="text-sm font-normal text-gray-400">/ мес</span></div>
                <p class="text-gray-400 mb-6 text-sm">Идеально для личных проектов и небольших сайтов.</p>
                <ul class="space-y-4 mb-8 text-sm text-gray-300">
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 1 vCPU</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 2 GB RAM</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 30 GB NVMe</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 1 IP адрес</li>
                </ul>
                <a href="#" class="block w-full text-center py-3 border border-white/20 rounded-lg hover:bg-white hover:text-black transition-colors font-bold">Заказать</a>
            </div>

            <!-- Pro (Featured) -->
            <div class="bg-[#050508] border border-[#a6cb40] rounded-2xl p-8 relative transform md:-translate-y-4 shadow-[0_0_30px_rgba(166,203,64,0.15)]">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#a6cb40] text-[#0a0a0f] px-4 py-1 rounded-full text-xs font-bold uppercase">Популярный</div>
                <h3 class="text-xl font-bold mb-2">Pro</h3>
                <div class="text-4xl font-bold text-white mb-6">1 200 ₽ <span class="text-sm font-normal text-gray-400">/ мес</span></div>
                <p class="text-gray-400 mb-6 text-sm">Для растущих проектов и игровых серверов.</p>
                <ul class="space-y-4 mb-8 text-sm text-gray-300">
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 4 vCPU</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 8 GB RAM</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 100 GB NVMe</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Бэкапы включены</li>
                </ul>
                <a href="#" class="block w-full text-center py-3 bg-[#a6cb40] text-[#0a0a0f] rounded-lg hover:bg-[#8eb330] transition-colors font-bold shadow-lg">Заказать</a>
            </div>

            <!-- Business -->
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 hover:border-[#a6cb40]/30 transition-all">
                <h3 class="text-xl font-bold mb-2">Business</h3>
                <div class="text-4xl font-bold text-white mb-6">4 500 ₽ <span class="text-sm font-normal text-gray-400">/ мес</span></div>
                <p class="text-gray-400 mb-6 text-sm">Максимальная производительность для бизнеса.</p>
                <ul class="space-y-4 mb-8 text-sm text-gray-300">
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 16 vCPU</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 32 GB RAM</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 500 GB NVMe</li>
                    <li class="flex items-center gap-3"><svg class="w-5 h-5 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Приоритетная поддержка</li>
                </ul>
                <a href="#" class="block w-full text-center py-3 border border-white/20 rounded-lg hover:bg-white hover:text-black transition-colors font-bold">Заказать</a>
            </div>
        </div>
    </div>
</section>
@endsection
