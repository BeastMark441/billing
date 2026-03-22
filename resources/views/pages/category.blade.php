@extends('layouts.landing')

@section('title', $category->name . ' — NODEUM')
@section('description', $category->description)

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('products') }}" class="hover:text-white transition-colors">Каталог</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li class="text-white font-medium">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $category->name }}</h1>
            <p class="text-lg text-gray-400 max-w-3xl">{{ $category->description }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($subcategories as $sub)
                <a href="{{ route('categories.subcategory', [$category->slug, $sub->slug]) }}" class="group bg-[#050508] border border-white/10 rounded-2xl p-8 hover:border-[#a6cb40]/50 transition-all">
                    <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-[#a6cb40] transition-colors">{{ $sub->name }}</h3>
                    <p class="text-gray-400 text-sm mb-6">{{ $sub->description }}</p>
                    <div class="flex items-center text-[#a6cb40] font-semibold text-sm">
                        Смотреть тарифы
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
