@extends('layouts.landing')

@section('title', ($service->name ?? 'Услуга').' — NODEUM')
@section('description', $service->description ?: ('Тариф '.$service->name.' в категории '.$category->name.'. Актуальная цена и характеристики.'))

@section('content')
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">
            <div class="max-w-3xl">
                <div class="text-sm text-gray-400 mb-3">
                    <a href="{{ route('products') }}" class="hover:underline">Каталог</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('products.category', $category) }}" class="hover:underline">{{ $category->name }}</a>
                </div>

                <h1 class="text-4xl font-bold text-white leading-tight">{{ $service->name }}</h1>

                @if($service->description)
                    <p class="mt-4 text-lg text-gray-400 leading-relaxed">{{ $service->description }}</p>
                @endif

                @if(is_array($service->specifications) && count($service->specifications) > 0)
                    <div class="mt-8 bg-[#050508] border border-white/10 rounded-2xl p-6">
                        <h2 class="text-lg font-bold text-white mb-4">Характеристики</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php
                                $specLabels = [
                                    'memory' => 'ОЗУ',
                                    'disk' => 'Диск',
                                    'cpu' => 'CPU',
                                    'databases' => 'Базы данных',
                                    'backups' => 'Резервные копии',
                                    'allocations' => 'Порты',
                                ];

                                $hiddenSpecKeys = ['egg_id', 'io', 'swap', 'spaw'];

                                $formatSizeMb = function ($mb) {
                                    $mb = (float) $mb;
                                    if ($mb >= 1024) {
                                        $gb = $mb / 1024;
                                        $formatted = rtrim(rtrim(number_format($gb, 1, '.', ''), '0'), '.');
                                        return $formatted.' ГБ';
                                    }
                                    return (string) ((int) $mb).' МБ';
                                };
                            @endphp

                            @foreach($service->specifications as $key => $value)
                                @php
                                    $rawKey = is_string($key) ? $key : '';
                                    $normalizedKey = strtolower(trim($rawKey));

                                    if ($normalizedKey !== '' && in_array($normalizedKey, $hiddenSpecKeys, true)) {
                                        continue;
                                    }

                                    $label = $rawKey !== '' ? ($specLabels[$normalizedKey] ?? $rawKey) : 'Параметр';

                                    $displayValue = '';
                                    if (is_scalar($value)) {
                                        if (in_array($normalizedKey, ['memory', 'disk'], true) && is_numeric($value)) {
                                            $displayValue = $formatSizeMb($value);
                                        } elseif ($normalizedKey === 'cpu' && is_numeric($value)) {
                                            $displayValue = (string) ((int) $value).'%';
                                        } else {
                                            $displayValue = (string) $value;
                                        }
                                    } else {
                                        $displayValue = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                @endphp

                                <div class="flex items-start justify-between gap-3 bg-white/5 rounded-lg px-4 py-3 min-w-0">
                                    <div class="text-sm text-gray-400">{{ $label }}</div>
                                    <div class="text-sm text-white text-right break-words min-w-0">{{ $displayValue }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="w-full lg:w-[380px]">
                <div class="bg-[#050508] border border-white/10 rounded-2xl p-6 sticky top-6">
                    <div class="text-xs text-gray-400">Цена</div>
                    <div class="mt-2 text-3xl font-bold text-white">{{ number_format((float) $service->price, 0, '.', ' ') }} ₽ <span class="text-base font-normal text-gray-400">/ мес</span></div>

                    <div class="mt-6 grid grid-cols-1 gap-3">
                        @auth
                            <a href="{{ route('dashboard.infrastructure') }}" class="w-full text-center px-5 py-3 rounded-xl bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-colors">Перейти к заказу</a>
                        @else
                            <a href="{{ route('register') }}" class="w-full text-center px-5 py-3 rounded-xl bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-colors">Создать аккаунт</a>
                            <a href="{{ route('login') }}" class="w-full text-center px-5 py-3 rounded-xl border border-white/15 hover:bg-white/5 text-white font-semibold transition-colors">Войти</a>
                        @endauth
                    </div>

                    <div class="mt-6 text-xs text-gray-500">Нужна консультация? Напишите в поддержку через кабинет после входа.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $service->name,
    'description' => $service->description ?: ($service->name.' — тариф NODEUM'),
    'brand' => ['@type' => 'Brand', 'name' => 'NODEUM'],
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'RUB',
        'price' => (float) $service->price,
        'availability' => 'https://schema.org/InStock',
        'url' => route('products.service', [$category, $service]),
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection
