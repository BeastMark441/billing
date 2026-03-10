<nav x-data="{ open: false }" class="bg-[#050508] border-b border-white/10">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-[#a6cb40]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span class="font-bold text-xl tracking-tight text-white">NODEUM</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-[#a6cb40] hover:border-[#a6cb40] focus:border-[#a6cb40]">
                        {{ __('Панель управления') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications -->
                <div class="relative mr-4" x-data="{ open: false }">
                    <button @click="open = !open" class="text-gray-400 hover:text-white relative p-2 rounded-full hover:bg-white/5 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-[#050508]">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-[#1a1a20] border border-white/10 rounded-xl shadow-xl py-2 z-50 origin-top-right" style="display: none;">
                        <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center">
                            <span class="text-sm font-bold text-white">Уведомления</span>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="text-xs text-blue-400 hover:text-blue-300 font-medium transition-colors">Прочитать все</button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-[320px] overflow-y-auto custom-scrollbar">
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
                                        <div class="text-sm font-medium text-white truncate pr-6">{{ $notification->data['title'] ?? 'Уведомление' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $notification->data['message'] ?? '' }}</div>
                                        <div class="text-[10px] text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                    @if(!$notification->read_at)
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="absolute top-3 right-3 text-gray-500 hover:text-white opacity-0 group-hover:opacity-100 transition-opacity" title="Пометить как прочитанное">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </a>
                                    @endif
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
                            <a href="{{ route('notifications.index') }}" class="block w-full py-1.5 text-xs text-center text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                                Показать все уведомления
                            </a>
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-[#0a0a0f] hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-[#0a0a0f] border border-white/10 text-white">
                            <x-dropdown-link :href="route('profile.edit')" class="text-gray-300 hover:bg-[#a6cb40] hover:text-[#0a0a0f]">
                                {{ __('Профиль') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')" class="text-gray-300 hover:bg-[#a6cb40] hover:text-[#0a0a0f]"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Выйти') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#050508]">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-[#a6cb40] hover:border-[#a6cb40] hover:bg-[#a6cb40]/10">
                {{ __('Панель управления') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/10">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-300 hover:text-[#a6cb40] hover:bg-[#a6cb40]/10">
                    {{ __('Профиль') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" class="text-gray-300 hover:text-[#a6cb40] hover:bg-[#a6cb40]/10"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Выйти') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
