@extends('layouts.landing')

@section('title', $subcategory->name . ' — ' . $category->name . ' — NODEUM')
@section('description', $subcategory->description)

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('products') }}" class="hover:text-white transition-colors">Каталог</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li><a href="{{ route('categories.show', $category->slug) }}" class="hover:text-white transition-colors">{{ $category->name }}</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li class="text-white font-medium">{{ $subcategory->name }}</li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8 mb-12">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $subcategory->name }}</h1>
                <p class="text-lg text-gray-400 max-w-3xl">{{ $subcategory->description }}</p>
            </div>
            
            <!-- Filters & Sorting -->
            <form method="GET" class="flex flex-wrap gap-4 bg-[#050508] border border-white/10 p-4 rounded-xl">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 uppercase">Сортировка:</span>
                    <select name="sort" onchange="this.form.submit()" class="bg-transparent border-none text-white text-sm focus:ring-0 cursor-pointer">
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Сначала дешевые</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Сначала дорогие</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>По названию</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                            <th class="px-8 py-5 font-semibold">Тарифный план</th>
                            <th class="px-8 py-5 font-semibold">Характеристики</th>
                            <th class="px-8 py-5 font-semibold">Стоимость</th>
                            <th class="px-8 py-5 font-semibold text-right">Действие</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($services as $service)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-8 py-6">
                                <div class="text-lg font-bold text-white mb-1">{{ $service->name }}</div>
                                <div class="text-sm text-gray-500">{{ $service->description }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($service->specifications ?? [] as $key => $value)
                                        <span class="text-[10px] bg-white/5 text-gray-400 px-2 py-1 rounded uppercase">
                                            {{ $key }}: <span class="text-gray-200">{{ $value }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-2xl font-bold text-[#a6cb40] font-mono">{{ number_format($service->price, 0) }} ₽ <span class="text-xs text-gray-500 font-sans">/ мес</span></div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @auth
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('cart.add', $service) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-3 rounded-lg border border-white/10 hover:bg-white/5 text-white transition-colors" title="В корзину">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('orders.create', $service) }}" class="px-6 py-3 rounded-lg bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-all shadow-lg shadow-[#a6cb40]/10">Заказать</a>
                                    </div>
                                @else
                                    <a href="{{ route('register') }}" class="px-8 py-3 rounded-lg bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-all shadow-lg shadow-[#a6cb40]/10">Начать работу</a>
                                @endauth
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-500 italic">Тарифы временно отсутствуют</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
