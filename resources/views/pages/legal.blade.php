@extends('layouts.landing')

@section('title', 'Юридические документы — NODEUM')
@section('description', 'Пользовательское соглашение, оферта, правила сервиса, политика обработки данных и cookies.')

@section('content')
<section class="py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white">Юридические документы</h1>
            <p class="mt-3 text-lg text-gray-400">Актуальные правила, соглашения и политики.</p>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-2xl overflow-hidden">
            <div class="divide-y divide-white/5">
                @foreach($docs as $key => $doc)
                    <a href="{{ route('legal.doc', $key) }}" class="block px-6 py-5 hover:bg-white/5 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-white font-semibold">{{ $doc['title'] }}</div>
                            <div class="text-sm text-[#a6cb40]">Открыть</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection

