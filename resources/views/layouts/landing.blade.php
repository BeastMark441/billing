<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'NODEUM — Хостинг и инфраструктура для проектов')</title>
    <meta name="description" content="@yield('description', 'NODEUM — хостинг-решения: VDS/VPS, игровые серверы, базы данных. Быстрый запуск, прозрачные цены, поддержка 24/7.')">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="NODEUM">
    <meta property="og:title" content="@yield('title', 'NODEUM — Хостинг и инфраструктура для проектов')">
    <meta property="og:description" content="@yield('description', 'NODEUM — хостинг-решения: VDS/VPS, игровые серверы, базы данных. Быстрый запуск, прозрачные цены, поддержка 24/7.')">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'NODEUM — Хостинг и инфраструктура для проектов')">
    <meta name="twitter:description" content="@yield('description', 'NODEUM — хостинг-решения: VDS/VPS, игровые серверы, базы данных. Быстрый запуск, прозрачные цены, поддержка 24/7.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Fallback for direct serving if vite not built -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'brand-dark': '#0a0a0f',
                            'brand-green': '#a6cb40',
                        },
                        fontFamily: {
                            sans: ['Instrument Sans', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif

    @include('partials.analytics')

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'NODEUM',
            'url' => config('app.url'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'NODEUM',
            'url' => config('app.url'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
</head>
<body class="bg-[#0a0a0f] text-white font-sans antialiased min-h-screen flex flex-col">

    <!-- Header -->
    <header class="fixed w-full top-0 z-50 bg-[#0a0a0f]/90 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Left: Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <a href="/" class="flex items-center gap-2 group">
                        <!-- Simple Hexagon/Node SVG Logo -->
                        <svg class="w-8 h-8 text-[#a6cb40] transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="font-bold text-2xl tracking-tight text-white">NODEUM</span>
                    </a>
                </div>

                <!-- Center: Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('products') }}" class="text-gray-300 hover:text-[#a6cb40] transition-colors text-sm font-medium {{ request()->routeIs('products') ? 'text-[#a6cb40]' : '' }}">Продукты</a>
                    <a href="{{ route('solutions') }}" class="text-gray-300 hover:text-[#a6cb40] transition-colors text-sm font-medium {{ request()->routeIs('solutions') ? 'text-[#a6cb40]' : '' }}">Решения</a>
                    <a href="{{ route('pricing') }}" class="text-gray-300 hover:text-[#a6cb40] transition-colors text-sm font-medium {{ request()->routeIs('pricing') ? 'text-[#a6cb40]' : '' }}">Цены</a>
                    <a href="{{ route('about') }}" class="text-gray-300 hover:text-[#a6cb40] transition-colors text-sm font-medium {{ request()->routeIs('about') ? 'text-[#a6cb40]' : '' }}">О компании</a>
                </nav>

                <!-- Right: Client Area -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- Authenticated User -->
                        <div class="hidden lg:flex items-center gap-4 border-r border-white/10 pr-4 mr-1">
                             <a href="{{ route('dashboard.tickets.create') }}" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Поддержка
                             </a>
                             <a href="{{ route('dashboard.billing') }}" class="text-sm font-medium text-white transition-colors flex items-center gap-2">
                                <span class="text-gray-400 text-xs">Баланс:</span>
                                <span class="text-[#a6cb40]">{{ number_format(Auth::user()->balance, 2, '.', ' ') }} ₽</span>
                             </a>
                        </div>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 focus:outline-none">
                                <div class="text-right hidden sm:block">
                                    <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Auth::user()->role_label }}</div>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-[#a6cb40] flex items-center justify-center text-[#0a0a0f] font-bold border-2 border-[#a6cb40]/20">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-[#050508] border border-white/10 rounded-xl shadow-xl py-2 z-50">
                                <div class="px-4 py-2 border-b border-white/10 sm:hidden">
                                    <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-[#a6cb40]">{{ number_format(Auth::user()->balance, 2, '.', ' ') }} ₽</div>
                                </div>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Панель управления</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Профиль</a>
                                <a href="{{ route('dashboard.billing') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Баланс</a>
                                <div class="border-t border-white/10 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300">Выйти</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="text-sm font-medium text-white hover:text-[#a6cb40] transition-colors">Войти</a>
                            <a href="{{ route('register') }}" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] px-4 py-2 rounded-md text-sm font-bold transition-all shadow-[0_0_15px_rgba(166,203,64,0.3)] hover:shadow-[0_0_20px_rgba(166,203,64,0.5)]">
                                Регистрация
                            </a>
                        </div>
                    @endauth

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center ml-2">
                        <button class="text-gray-300 hover:text-white focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#050508] border-t border-white/5 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-6 h-6 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="font-bold text-xl text-white">NODEUM</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Надежные решения для вашего бизнеса и игровых проектов. VDS, хостинг, базы данных.
                    </p>

                    <div class="mt-5">
                        <div class="text-white font-semibold mb-3">Мы в соцсетях</div>
                        <div class="flex flex-wrap gap-2 text-sm">
                            @php
                                $social = config('services.social');
                            @endphp
                            <a href="{{ $social['vk'] ?? '#' }}" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition-colors" target="_blank" rel="noreferrer">VK</a>
                            <a href="{{ $social['max'] ?? '#' }}" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition-colors" target="_blank" rel="noreferrer">MAX</a>
                            <a href="{{ $social['youtube'] ?? '#' }}" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition-colors" target="_blank" rel="noreferrer">YouTube</a>
                            <a href="{{ $social['discord'] ?? '#' }}" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition-colors" target="_blank" rel="noreferrer">Discord</a>
                            <a href="{{ $social['telegram'] ?? '#' }}" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition-colors" target="_blank" rel="noreferrer">Telegram</a>
                            <a href="{{ $social['rutube'] ?? '#' }}" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition-colors" target="_blank" rel="noreferrer">RuTube</a>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Продукты</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('products') }}" class="hover:text-[#a6cb40]">VDS/VPS</a></li>
                        <li><a href="{{ route('products') }}" class="hover:text-[#a6cb40]">Игровой хостинг</a></li>
                        <li><a href="{{ route('products') }}" class="hover:text-[#a6cb40]">Выделенные серверы</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Компания</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('about') }}" class="hover:text-[#a6cb40]">О нас</a></li>
                        <li><a href="{{ route('contacts') }}" class="hover:text-[#a6cb40]">Контакты</a></li>
                        <li><a href="{{ route('blog') }}" class="hover:text-[#a6cb40]">Блог</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Поддержка</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('knowledge-base') }}" class="hover:text-[#a6cb40]">База знаний</a></li>
                        <li><a href="{{ route('status') }}" class="hover:text-[#a6cb40]">Статус серверов</a></li>
                        <li><a href="{{ route('api-docs') }}" class="hover:text-[#a6cb40]">API Документация</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Документы</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('legal.doc', 'user-agreement') }}" class="hover:text-[#a6cb40]">Пользовательское соглашение</a></li>
                        <li><a href="{{ route('legal.doc', 'service-rules') }}" class="hover:text-[#a6cb40]">Правила пользования</a></li>
                        <li><a href="{{ route('legal.doc', 'offer') }}" class="hover:text-[#a6cb40]">Договор-оферта</a></li>
                        <li><a href="{{ route('legal.doc', 'privacy') }}" class="hover:text-[#a6cb40]">Обработка данных</a></li>
                        <li><a href="{{ route('legal.doc', 'cookies') }}" class="hover:text-[#a6cb40]">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-white/5 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} NODEUM.RU. Все права защищены.
            </div>
        </div>
    </footer>

    <x-cookie-consent />

</body>
</html>
