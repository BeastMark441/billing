<x-app-layout>
    <div class="space-y-8" x-data="infrastructureSearch()">
        <!-- Header & Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Инфраструктура</h2>
                <p class="text-sm text-gray-400">Выберите сервис для создания и управления.</p>
            </div>
            
            <div class="relative w-full md:w-96">
                <input type="text" 
                       x-model="searchQuery" 
                       @input.debounce.300ms="performSearch"
                       placeholder="Искать по названию (мин. 2 символа)" 
                       class="w-full bg-[#050508] border border-white/10 rounded-lg pl-4 pr-10 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-[#a6cb40] focus:ring-1 focus:ring-[#a6cb40] transition-colors">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Search Results Modal -->
        <div x-show="showResults" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showResults = false"
             class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 pointer-events-none">
            
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showResults = false"></div>
            
            <div class="relative bg-[#0f0f13] border border-white/10 rounded-2xl w-full max-w-4xl max-h-[80vh] overflow-hidden flex flex-col pointer-events-auto shadow-2xl">
                <div class="p-6 border-b border-white/10 flex justify-between items-center bg-[#0a0a0f]">
                    <h3 class="text-xl font-bold text-white">Результаты поиска</h3>
                    <button @click="showResults = false" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <template x-if="searchResults.length === 0">
                        <div class="text-center text-gray-500 py-8">
                            Ничего не найдено по вашему запросу
                        </div>
                    </template>
                    
                    <template x-for="service in searchResults" :key="service.id">
                        <div class="bg-white/5 hover:bg-white/10 rounded-xl p-4 transition-all border border-white/5 hover:border-[#a6cb40]/30 group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400" x-text="service.category_name"></span>
                                        <template x-if="service.subcategory_name">
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-purple-500/20 text-purple-400" x-text="service.subcategory_name"></span>
                                        </template>
                                    </div>
                                    <h4 class="text-lg font-bold text-white group-hover:text-[#a6cb40] transition-colors" x-text="service.name"></h4>
                                    <p class="text-sm text-gray-400 mt-1" x-text="service.description"></p>
                                    
                                    <!-- Specs -->
                                    <template x-if="service.specifications">
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <template x-for="(value, key) in service.specifications" :key="key">
                                                <span class="text-xs text-gray-300 bg-black/30 px-2 py-1 rounded border border-white/5">
                                                    <span class="opacity-50 uppercase mr-1" x-text="key + ':'"></span>
                                                    <span x-text="value"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="flex flex-col items-end gap-3 min-w-[120px]">
                                    <div class="text-xl font-mono text-white font-bold">
                                        <span x-text="new Intl.NumberFormat('ru-RU').format(service.price)"></span> ₽
                                    </div>
                                    <a :href="`/infrastructure/services/${service.id}/order`" class="px-4 py-2 bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] font-bold rounded-lg transition-colors w-full text-sm text-center">
                                        Заказать
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        @if($orders->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl font-bold text-white mb-4">Мои активные услуги</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($orders as $order)
                <div class="bg-[#0f0f13] border {{ $order->status === 'failed' ? 'border-red-500/20' : 'border-white/5' }} rounded-xl p-4 flex flex-col justify-between hover:border-white/10 transition-colors">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $order->status === 'active' ? 'bg-green-500' : ($order->status === 'pending' ? 'bg-yellow-500' : ($order->status === 'failed' ? 'bg-red-500' : 'bg-gray-500')) }}"></span>
                                <h4 class="font-bold text-white text-sm">{{ $order->service->name }}</h4>
                            </div>
                            <span class="text-xs text-gray-500">#{{ $order->id }}</span>
                        </div>
                        <div class="text-xs {{ $order->status === 'failed' ? 'text-red-400' : 'text-gray-400' }} mb-3">
                            @if($order->status === 'failed')
                                Ошибка установки
                            @elseif($order->server_ip)
                                {{ $order->server_ip }}:{{ $order->server_port }}
                            @else
                                Ожидает установки
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-2 pt-3 border-t border-white/5">
                        <span class="text-xs font-mono text-gray-300">{{ number_format($order->price, 0) }} ₽/мес</span>
                        <a href="{{ route('orders.show', $order) }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition-colors">
                            Управление &rarr;
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Catalog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($categories as $category)
            <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-5 hover:border-white/10 transition-colors flex flex-col h-full">
                <div class="mb-4 pb-4 border-b border-white/5">
                    <h3 class="text-lg font-bold text-white mb-1">{{ $category->name }}</h3>
                    @if($category->description)
                    <p class="text-xs text-gray-400 line-clamp-2">{{ $category->description }}</p>
                    @endif
                </div>

                <div class="flex-1 space-y-4 overflow-y-auto max-h-[400px] pr-1 custom-scrollbar">
                    <!-- Subcategories -->
                    @foreach($category->subcategories as $subcategory)
                        <div class="space-y-2" x-data="{ expanded: false }">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider pl-1 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                {{ $subcategory->name }}
                            </h4>
                            <div class="space-y-2">
                                @forelse($subcategory->services as $index => $service)
                                <a href="{{ route('orders.create', $service) }}" 
                                   class="block bg-white/5 hover:bg-white/10 rounded-lg p-3 transition-all group border border-transparent hover:border-white/10"
                                   x-show="expanded || {{ $index }} < 3"
                                   x-cloak>
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-medium text-white text-sm group-hover:text-[#a6cb40] transition-colors line-clamp-1">{{ $service->name }}</span>
                                        <span class="text-white font-mono text-xs whitespace-nowrap ml-2">{{ number_format($service->price, 0) }} ₽</span>
                                    </div>
                                    @if($service->description)
                                    <p class="text-[10px] text-gray-500 line-clamp-1 group-hover:text-gray-400 transition-colors">{{ $service->description }}</p>
                                    @endif
                                </a>
                                @empty
                                <div class="text-[10px] text-gray-600 pl-3 italic">Нет тарифов</div>
                                @endforelse

                                @if($subcategory->services->count() > 3)
                                <button @click="expanded = !expanded" 
                                        class="w-full text-center py-2 text-xs font-medium text-gray-500 hover:text-white transition-colors border border-white/5 rounded-lg hover:bg-white/5 flex items-center justify-center gap-1">
                                    <span x-text="expanded ? 'Скрыть' : 'Показать все ({{ $subcategory->services->count() }})'"></span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Direct Services -->
                    @if($category->services->count() > 0)
                        <div class="space-y-2 {{ $category->subcategories->count() > 0 ? 'pt-2 border-t border-white/5' : '' }}">
                            @if($category->subcategories->count() > 0)
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider pl-1 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                Другое
                            </h4>
                            @endif
                            
                            @foreach($category->services as $service)
                            <a href="{{ route('orders.create', $service) }}" class="block bg-white/5 hover:bg-white/10 rounded-lg p-3 transition-all group border border-transparent hover:border-white/10">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-medium text-white text-sm group-hover:text-[#a6cb40] transition-colors line-clamp-1">{{ $service->name }}</span>
                                    <span class="text-white font-mono text-xs whitespace-nowrap ml-2">{{ number_format($service->price, 0) }} ₽</span>
                                </div>
                                @if($service->description)
                                <p class="text-[10px] text-gray-500 line-clamp-1 group-hover:text-gray-400 transition-colors">{{ $service->description }}</p>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    @endif

                    @if($category->subcategories->isEmpty() && $category->services->isEmpty())
                        <div class="text-center py-8 text-gray-500 text-xs italic">
                            Категория пуста
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-12">
                <p>Категории инфраструктуры не найдены.</p>
            </div>
            @endforelse
        </div>
    </div>

    <script>
        function infrastructureSearch() {
            return {
                searchQuery: '',
                searchResults: [],
                showResults: false,
                
                async performSearch() {
                    if (this.searchQuery.length < 2) {
                        this.showResults = false;
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/api/infrastructure/search?q=${encodeURIComponent(this.searchQuery)}`);
                        if (response.ok) {
                            this.searchResults = await response.json();
                            this.showResults = true;
                        }
                    } catch (error) {
                        console.error('Search failed:', error);
                    }
                }
            }
        }
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</x-app-layout>
