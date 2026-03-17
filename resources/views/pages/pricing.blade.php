@extends('layouts.landing')

@section('title', 'Цены — NODEUM')
@section('description', 'Прозрачные цены на услуги NODEUM. Актуальные тарифы по категориям, без скрытых платежей.')

@section('content')
<section class="py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-14">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Прозрачные <span class="text-[#a6cb40]">цены</span></h1>
            <p class="text-lg text-gray-400 max-w-2xl mx-auto">Все тарифы подгружаются из панели управления и всегда актуальны.</p>
        </div>

        <div class="space-y-10">
            @forelse($categories as $cat)
                @php
                    $services = $cat->services ?? collect();
                @endphp
                <div class="bg-[#050508] border border-white/10 rounded-2xl overflow-hidden">
                    <div class="p-6 md:p-8 border-b border-white/5">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ $cat->name }}</h2>
                                @if($cat->description)
                                    <p class="text-sm text-gray-400 mt-1 max-w-3xl">{{ $cat->description }}</p>
                                @endif
                            </div>
                            <a href="{{ route('products.category', $cat) }}" class="text-sm text-[#a6cb40] hover:underline">Смотреть в каталоге</a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-white/5">
                                    <th class="px-6 py-4 font-medium">Тариф</th>
                                    <th class="px-6 py-4 font-medium">Описание</th>
                                    <th class="px-6 py-4 font-medium">Цена</th>
                                    <th class="px-6 py-4 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($services as $service)
                                    <tr class="text-sm">
                                        <td class="px-6 py-4 text-white font-semibold whitespace-nowrap">{{ $service->name }}</td>
                                        <td class="px-6 py-4 text-gray-400">{{ $service->description ?: '—' }}</td>
                                        <td class="px-6 py-4 text-white font-bold whitespace-nowrap">{{ number_format((float) $service->price, 0, '.', ' ') }} ₽ <span class="text-xs text-gray-500 font-normal">/ мес</span></td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <a href="{{ route('products.service', [$cat, $service]) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-white/15 hover:bg-white/5 text-white text-sm font-semibold transition-colors">Подробнее</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">В этой категории пока нет активных тарифов.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-[#050508] border border-white/10 rounded-2xl p-10 text-center text-gray-400">Прайс-лист пока пуст. Добавьте категории и тарифы в админ‑панели.</div>
            @endforelse
        </div>
    </div>
</section>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'NODEUM',
    'url' => config('app.url'),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection

