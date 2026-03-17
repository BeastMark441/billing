@extends('layouts.landing')

@section('title', 'Каталог услуг — NODEUM')
@section('description', 'Каталог услуг NODEUM: VDS/VPS, игровые решения, базы данных и другие сервисы. Актуальные тарифы и быстрый запуск.')

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Каталог <span class="text-[#a6cb40]">услуг</span></h1>
                <p class="text-lg text-gray-400 max-w-2xl">Актуальные категории и тарифы подгружаются из панели управления.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pricing') }}" class="px-5 py-3 rounded-lg border border-white/15 hover:border-[#a6cb40]/50 hover:bg-white/5 text-white font-semibold transition-colors">Посмотреть цены</a>
                <a href="{{ route('register') }}" class="px-5 py-3 rounded-lg bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-colors shadow-[0_0_20px_rgba(166,203,64,0.25)]">Создать аккаунт</a>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-10">
            <a href="{{ route('products') }}" class="px-4 py-2 rounded-full text-sm font-semibold border transition-colors {{ $selectedCategory ? 'border-white/10 text-gray-300 hover:bg-white/5' : 'border-[#a6cb40]/50 bg-[#a6cb40]/10 text-white' }}">Все</a>
            @foreach($categories as $cat)
                <a href="{{ route('products.category', $cat) }}" class="px-4 py-2 rounded-full text-sm font-semibold border transition-colors {{ $selectedCategory && $selectedCategory->id === $cat->id ? 'border-[#a6cb40]/50 bg-[#a6cb40]/10 text-white' : 'border-white/10 text-gray-300 hover:bg-white/5' }}">{{ $cat->name }}</a>
            @endforeach
        </div>

        @php
            $list = $selectedCategory ? collect([$selectedCategory]) : $categories;
        @endphp

        <div class="space-y-12">
            @forelse($list as $cat)
                @php
                    $services = $cat->relationLoaded('services') ? $cat->services : $cat->services()->where('is_active', true)->orderBy('sort_order')->orderBy('price')->get();
                @endphp

                <div class="bg-[#050508] border border-white/10 rounded-2xl p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-white">{{ $cat->name }}</h2>
                            @if($cat->description)
                                <p class="text-sm text-gray-400 mt-1 max-w-2xl">{{ $cat->description }}</p>
                            @endif
                        </div>
                        <a href="{{ route('products.category', $cat) }}" class="text-sm text-[#a6cb40] hover:underline">Открыть категорию</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($services as $service)
                            <div class="bg-[#0a0a0f] border border-white/10 rounded-xl p-5 hover:border-[#a6cb40]/40 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-white leading-snug">{{ $service->name }}</h3>
                                        @if($service->description)
                                            <p class="text-sm text-gray-400 mt-2 line-clamp-3">{{ $service->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-gray-400">от</div>
                                        <div class="text-xl font-bold text-white">{{ number_format((float) $service->price, 0, '.', ' ') }} ₽</div>
                                        <div class="text-xs text-gray-500">/ мес</div>
                                    </div>
                                </div>

                                <div class="mt-5 flex gap-3">
                                    <a href="{{ route('products.service', [$cat, $service]) }}" class="flex-1 text-center px-4 py-2 rounded-lg border border-white/15 hover:bg-white/5 text-white text-sm font-semibold transition-colors">Подробнее</a>
                                    @auth
                                        <a href="{{ route('dashboard.infrastructure') }}" class="flex-1 text-center px-4 py-2 rounded-lg bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] text-sm font-bold transition-colors">Заказать</a>
                                    @else
                                        <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 rounded-lg bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] text-sm font-bold transition-colors">Заказать</a>
                                    @endauth
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">В этой категории пока нет активных тарифов.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-[#050508] border border-white/10 rounded-2xl p-10 text-center text-gray-400">Каталог пока пуст. Добавьте категории и тарифы в админ‑панели.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection

