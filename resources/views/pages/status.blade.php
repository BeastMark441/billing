@extends('layouts.landing')

@section('title', 'Статус сервисов — NODEUM')
@section('description', 'Статус сервисов NODEUM и уведомления о плановых работах.')

@section('content')
<section class="py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white">Статус сервисов</h1>
            <p class="mt-3 text-lg text-gray-400">Здесь можно отображать аптайм и текущие инциденты.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-6">
                <div class="text-xs text-gray-400">Панель управления</div>
                <div class="mt-2 text-lg font-bold text-white">Работает</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-6">
                <div class="text-xs text-gray-400">Платежи</div>
                <div class="mt-2 text-lg font-bold text-white">Работает</div>
            </div>
            <div class="bg-[#050508] border border-white/10 rounded-2xl p-6">
                <div class="text-xs text-gray-400">Игровые панели</div>
                <div class="mt-2 text-lg font-bold text-white">Работает</div>
            </div>
        </div>

        <div class="mt-8 bg-[#050508] border border-white/10 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white">Инциденты</h2>
            <div class="mt-4 text-sm text-gray-400">Инцидентов нет.</div>
        </div>
    </div>
</section>
@endsection

