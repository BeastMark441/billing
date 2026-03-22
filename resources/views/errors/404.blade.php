@extends('layouts.landing')

@section('title', '404 — Страница не найдена — NODEUM')

@section('content')
<section class="min-h-[80vh] flex items-center justify-center relative overflow-hidden">
    <!-- Background glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#a6cb40]/10 rounded-full blur-[120px] -z-10"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="mb-8">
            <h1 class="text-[12rem] md:text-[18rem] font-black text-white/5 leading-none select-none">404</h1>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full">
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">Упс! Мы заблудились</h2>
                <p class="text-gray-400 text-lg md:text-xl max-w-xl mx-auto mb-12">
                    Похоже, страница, которую вы ищете, была перемещена, удалена или никогда не существовала. 
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ url('/') }}" class="px-8 py-4 bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] font-bold rounded-2xl transition-all shadow-xl shadow-[#a6cb40]/20 flex items-center gap-2 group">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Вернуться на главную
                    </a>
                    <a href="{{ route('dashboard.tickets.index') }}" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-white font-bold rounded-2xl transition-all border border-white/10 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Написать в поддержку
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
