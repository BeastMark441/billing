@extends('layouts.landing')

@section('title', 'Блог — NODEUM')
@section('description', 'Новости и статьи NODEUM о хостинге, инфраструктуре и безопасности.')

@section('content')
<section class="py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white">Блог</h1>
            <p class="mt-3 text-lg text-gray-400">Страница подготовлена для будущих публикаций.</p>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-2xl p-8 text-gray-300">
            <div class="text-sm text-gray-400">Скоро здесь появятся статьи и новости.</div>
            <div class="mt-6 flex gap-3">
                <a href="{{ route('products') }}" class="px-5 py-3 rounded-xl bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-colors">Каталог услуг</a>
                <a href="{{ route('pricing') }}" class="px-5 py-3 rounded-xl border border-white/15 hover:bg-white/5 text-white font-semibold transition-colors">Цены</a>
            </div>
        </div>
    </div>
</section>
@endsection

