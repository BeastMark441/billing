<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NODEUM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
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
            <style>
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: rgba(255, 255, 255, 0.05);
                    border-radius: 4px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 4px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: rgba(255, 255, 255, 0.3);
                }
            </style>
        @endif
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-[#0a0a0f] text-white">
        <div class="min-h-screen bg-[#0a0a0f]">
            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#050508] border-r border-white/10 hidden md:flex flex-col transition-transform duration-300">
                <div class="p-6 border-b border-white/10">
                     <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <svg class="w-8 h-8 text-[#a6cb40] transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="font-bold text-xl tracking-tight text-white">NODEUM</span>
                    </a>
                </div>

                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <!-- Account Section -->
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2 mt-4">Аккаунт</div>
                    
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('profile.edit') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Профиль
                    </a>

                    <a href="{{ route('dashboard.account') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.account') || request()->routeIs('dashboard') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 2-2 2h4c-1.5 0-2-1.116-2-2z"></path></svg>
                        Личная информация
                    </a>

                    <a href="{{ route('dashboard.security') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.security') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Безопасность
                    </a>

                    <!-- Infrastructure Section -->
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2 mt-6">Инфраструктура</div>
                    
                    <a href="{{ route('dashboard.infrastructure') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.infrastructure') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Сервисы и услуги
                    </a>

                    <!-- Billing Section -->
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2 mt-6">Биллинг</div>

                    <a href="{{ route('dashboard.billing') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.billing') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Обзор
                    </a>

                    <!-- Help Section (Dropdown) -->
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2 mt-6">Поддержка</div>

                    <div x-data="{ open: {{ request()->is('dashboard/tickets*') || request()->is('dashboard/status') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Помощь
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" x-collapse class="pl-11 pr-2 space-y-1 mt-1">
                            <a href="{{ route('dashboard.tickets.create') }}" class="block py-2 text-sm text-gray-400 hover:text-[#a6cb40] transition-colors {{ request()->routeIs('dashboard.tickets.create') ? 'text-[#a6cb40]' : '' }}">
                                Создать тикет
                            </a>
                            <a href="{{ route('dashboard.tickets.index', ['status' => 'open']) }}" class="block py-2 text-sm text-gray-400 hover:text-[#a6cb40] transition-colors {{ request()->fullUrlIs(route('dashboard.tickets.index', ['status' => 'open'])) ? 'text-[#a6cb40]' : '' }}">
                                Открытые тикеты
                            </a>
                            <a href="{{ route('dashboard.tickets.index') }}" class="block py-2 text-sm text-gray-400 hover:text-[#a6cb40] transition-colors {{ request()->routeIs('dashboard.tickets.index') && !request()->has('status') ? 'text-[#a6cb40]' : '' }}">
                                Все тикеты
                            </a>
                            <a href="{{ route('dashboard.status') }}" class="block py-2 text-sm text-gray-400 hover:text-[#a6cb40] transition-colors {{ request()->routeIs('dashboard.status') ? 'text-[#a6cb40]' : '' }}">
                                Статус систем
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-white/10 my-4"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Выйти
                        </button>
                    </form>
                </nav>

                <div class="p-4 border-t border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#a6cb40] flex items-center justify-center text-[#0a0a0f] font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</div>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-white relative p-2 rounded-full hover:bg-white/5 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        @if(Auth::user()->unreadNotifications->count() > 0)
                                            <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-[#050508]">
                                                {{ Auth::user()->unreadNotifications->count() }}
                                            </span>
                                        @endif
                                    </button>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute bottom-12 left-full ml-3 w-[min(20rem,calc(100vw-1.5rem))] bg-[#1a1a20] border border-white/10 rounded-xl shadow-xl py-2 z-50 origin-bottom-left" style="display: none;">
                                        <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center">
                                            <span class="text-sm font-bold text-white">Уведомления</span>
                                            @if(Auth::user()->unreadNotifications->count() > 0)
                                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-blue-400 hover:text-blue-300 font-medium transition-colors">Прочитать все</button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="max-h-[min(320px,calc(100vh-10rem))] overflow-y-auto custom-scrollbar">
                                            @forelse(Auth::user()->notifications()->latest()->take(5)->get() as $notification)
                                                <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5 transition-colors group relative {{ $notification->read_at ? 'opacity-60' : '' }}">
                                                    <div class="flex items-start gap-3">
                                                        <div class="mt-1 flex-shrink-0">
                                                            @if(($notification->data['type'] ?? 'info') === 'success')
                                                                <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5"></div>
                                                            @elseif(($notification->data['type'] ?? 'info') === 'error')
                                                                <div class="w-2 h-2 rounded-full bg-red-500 mt-1.5"></div>
                                                            @elseif(($notification->data['type'] ?? 'info') === 'warning')
                                                                <div class="w-2 h-2 rounded-full bg-yellow-500 mt-1.5"></div>
                                                            @else
                                                                <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm font-medium text-white truncate pr-10">{{ $notification->data['title'] ?? 'Уведомление' }}</div>
                                                            <div class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $notification->data['message'] ?? '' }}</div>
                                                            <div class="text-[10px] text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                                        </div>
                                                        <div class="absolute top-3 right-3 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            @if(!$notification->read_at)
                                                                <a href="{{ route('notifications.read', $notification->id) }}" class="text-gray-500 hover:text-white" title="Пометить как прочитанное">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                </a>
                                                            @endif
                                                            <form method="POST" action="{{ route('notifications.delete', $notification->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-gray-500 hover:text-red-300" title="Удалить">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="px-4 py-8 text-center">
                                                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/5 mb-3 text-gray-500">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                                    </div>
                                                    <div class="text-sm text-gray-500">Нет новых уведомлений</div>
                                                </div>
                                            @endforelse
                                        </div>
                                        <div class="p-2 text-center border-t border-white/10 bg-[#1a1a20] rounded-b-xl">
                                            <a href="{{ route('notifications.index') }}" class="block w-full py-1.5 text-xs text-center text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">Показать все уведомления</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex flex-col min-h-screen md:pl-64 transition-all duration-300">
                <!-- Mobile Header -->
                <header class="md:hidden bg-[#050508] border-b border-white/10 p-4 flex items-center justify-between sticky top-0 z-40">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="font-bold text-xl tracking-tight text-white">NODEUM</span>
                    </a>
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </header>

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden md:hidden bg-[#050508] border-b border-white/10 p-4 space-y-2">
                     <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white' }}">Профиль</a>
                     <a href="{{ route('dashboard.account') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard.account') || request()->routeIs('dashboard') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white' }}">Личная информация</a>
                     <a href="{{ route('dashboard.security') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard.security') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white' }}">Безопасность</a>
                     
                     <div class="border-t border-white/10 my-2"></div>
                     <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2">Биллинг</div>
                     <a href="{{ route('dashboard.billing') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard.billing') ? 'bg-[#a6cb40]/10 text-[#a6cb40]' : 'text-gray-400 hover:text-white' }}">Обзор</a>

                     <div class="border-t border-white/10 my-2"></div>
                     <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2">Помощь</div>
                     <a href="{{ route('dashboard.tickets.create') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white">Создать тикет</a>
                     <a href="{{ route('dashboard.tickets.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white">Мои тикеты</a>
                     <a href="{{ route('dashboard.status') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white">Статус систем</a>

                     <div class="border-t border-white/10 my-2"></div>
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-red-400 hover:text-red-300">Выйти</button>
                    </form>
                </div>

                <main class="flex-1 p-6 md:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-cookie-consent />
    </body>
</html>
