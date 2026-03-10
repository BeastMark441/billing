<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Редактирование услуги</h1>
            <p class="text-gray-400">Измените параметры услуги</p>
        </div>
        <a href="{{ route('admin.infrastructure.services.index') }}" class="text-gray-400 hover:text-white transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.infrastructure.services.update', $service) }}">
            @csrf
            @method('PUT')

            <!-- Category -->
            <div class="mb-4">
                <label for="infrastructure_category_id" class="block text-sm font-medium text-gray-300 mb-2">Категория</label>
                <select name="infrastructure_category_id" id="infrastructure_category_id" required 
                        class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                        onchange="updateSubcategories()">
                    <option value="">Выберите категорию</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('infrastructure_category_id', $service->infrastructure_category_id) == $category->id ? 'selected' : '' }} data-subcategories="{{ json_encode($category->subcategories) }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('infrastructure_category_id')" class="mt-2 text-red-400" />
            </div>

            <!-- Subcategory -->
            <div class="mb-4" id="subcategory_container" style="display: none;">
                <label for="infrastructure_subcategory_id" class="block text-sm font-medium text-gray-300 mb-2">Подкатегория (опционально)</label>
                <select name="infrastructure_subcategory_id" id="infrastructure_subcategory_id" 
                        class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    <option value="">Без подкатегории</option>
                </select>
                <x-input-error :messages="$errors->get('infrastructure_subcategory_id')" class="mt-2 text-red-400" />
            </div>

            <script>
                function updateSubcategories() {
                    const categorySelect = document.getElementById('infrastructure_category_id');
                    const subcategoryContainer = document.getElementById('subcategory_container');
                    const subcategorySelect = document.getElementById('infrastructure_subcategory_id');
                    
                    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                    const subcategories = selectedOption.dataset.subcategories ? JSON.parse(selectedOption.dataset.subcategories) : [];
                    
                    // Store current selection if any
                    const currentSubcategory = "{{ old('infrastructure_subcategory_id', $service->infrastructure_subcategory_id) }}";
                    
                    subcategorySelect.innerHTML = '<option value="">Без подкатегории</option>';
                    
                    if (subcategories.length > 0) {
                        subcategoryContainer.style.display = 'block';
                        subcategories.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            if (sub.id == currentSubcategory) {
                                option.selected = true;
                            }
                            subcategorySelect.appendChild(option);
                        });
                    } else {
                        subcategoryContainer.style.display = 'none';
                    }
                }
                
                // Initialize on load
                document.addEventListener('DOMContentLoaded', updateSubcategories);
            </script>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Название услуги</label>
                <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                       placeholder="Например: Start Plan">
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400" />
            </div>

            <!-- Slug -->
            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-300 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $service->slug) }}" 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                       placeholder="Автоматически из названия">
                <x-input-error :messages="$errors->get('slug')" class="mt-2 text-red-400" />
            </div>

            <!-- Price -->
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Цена (₽)</label>
                <input type="number" name="price" id="price" value="{{ old('price', $service->price) }}" min="0" step="0.01" required
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                <x-input-error :messages="$errors->get('price')" class="mt-2 text-red-400" />
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Описание</label>
                <textarea name="description" id="description" rows="3" 
                          class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                          placeholder="Описание услуги">{{ old('description', $service->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-400" />
            </div>

            <!-- Pterodactyl Configuration -->
            <div class="mb-6 bg-[#0a0a0f] p-4 rounded-xl border border-white/10">
                <h3 class="text-lg font-semibold text-white mb-4">Настройки Pterodactyl (для игровых серверов)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Egg ID -->
                    <div>
                        <label for="pterodactyl_egg_id" class="block text-sm font-medium text-gray-400 mb-1">Egg ID</label>
                        <input type="number" name="pterodactyl[egg_id]" id="pterodactyl_egg_id" value="{{ old('pterodactyl.egg_id', $pterodactyl['egg_id'] ?? '') }}" required
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                        <p class="text-[10px] text-gray-500 mt-1">Остальные параметры (Nest, Image, Startup) подтянутся автоматически из Pterodactyl</p>
                    </div>
                    
                    <!-- Memory -->
                    <div>
                        <label for="pterodactyl_memory" class="block text-sm font-medium text-gray-400 mb-1">ОЗУ (Memory) MB</label>
                        <input type="number" name="pterodactyl[memory]" id="pterodactyl_memory" value="{{ old('pterodactyl.memory', $pterodactyl['memory'] ?? 1024) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Disk -->
                    <div>
                        <label for="pterodactyl_disk" class="block text-sm font-medium text-gray-400 mb-1">Диск (Disk) MB</label>
                        <input type="number" name="pterodactyl[disk]" id="pterodactyl_disk" value="{{ old('pterodactyl.disk', $pterodactyl['disk'] ?? 1024) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- CPU -->
                    <div>
                        <label for="pterodactyl_cpu" class="block text-sm font-medium text-gray-400 mb-1">CPU (%)</label>
                        <input type="number" name="pterodactyl[cpu]" id="pterodactyl_cpu" value="{{ old('pterodactyl.cpu', $pterodactyl['cpu'] ?? 100) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Swap -->
                    <div>
                        <label for="pterodactyl_swap" class="block text-sm font-medium text-gray-400 mb-1">Swap (MB)</label>
                        <input type="number" name="pterodactyl[swap]" id="pterodactyl_swap" value="{{ old('pterodactyl.swap', $pterodactyl['swap'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- IO -->
                    <div>
                        <label for="pterodactyl_io" class="block text-sm font-medium text-gray-400 mb-1">Block IO</label>
                        <input type="number" name="pterodactyl[io]" id="pterodactyl_io" value="{{ old('pterodactyl.io', $pterodactyl['io'] ?? 500) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Databases -->
                    <div>
                        <label for="pterodactyl_databases" class="block text-sm font-medium text-gray-400 mb-1">Лимит баз данных</label>
                        <input type="number" name="pterodactyl[databases]" id="pterodactyl_databases" value="{{ old('pterodactyl.databases', $pterodactyl['databases'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Backups -->
                    <div>
                        <label for="pterodactyl_backups" class="block text-sm font-medium text-gray-400 mb-1">Лимит бэкапов</label>
                        <input type="number" name="pterodactyl[backups]" id="pterodactyl_backups" value="{{ old('pterodactyl.backups', $pterodactyl['backups'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    <!-- Allocations -->
                    <div>
                        <label for="pterodactyl_allocations" class="block text-sm font-medium text-gray-400 mb-1">Доп. порты (Allocations)</label>
                        <input type="number" name="pterodactyl[allocations]" id="pterodactyl_allocations" value="{{ old('pterodactyl.allocations', $pterodactyl['allocations'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Sort Order -->
            <div class="mb-4">
                <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">Порядок сортировки</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $service->sort_order) }}" min="0"
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                <x-input-error :messages="$errors->get('sort_order')" class="mt-2 text-red-400" />
            </div>

            <!-- Is Active -->
            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }} 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-300">Активная услуга</span>
                </label>
                <x-input-error :messages="$errors->get('is_active')" class="mt-2 text-red-400" />
            </div>

            <!-- One Per User -->
            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="one_per_user" value="1" {{ old('one_per_user', $service->one_per_user) ? 'checked' : '' }} 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-300">Один на пользователя (бесплатный тариф)</span>
                </label>
                <x-input-error :messages="$errors->get('one_per_user')" class="mt-2 text-red-400" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.infrastructure.services.index') }}" 
                   class="px-6 py-2 border border-white/10 text-gray-300 hover:text-white hover:bg-white/5 rounded-lg font-medium transition-colors">
                    Отмена
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
