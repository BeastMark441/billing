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
                function applyIntegrationTypeFromCategoryName(categoryName) {
                    const integrationSelect = document.getElementById('integration_type');
                    if (! integrationSelect) {
                        return;
                    }
                    if (integrationSelect.dataset.auto === 'false') {
                        return;
                    }

                    const name = (categoryName || '').toLowerCase();
                    if (name.includes('игровые серверы')) {
                        integrationSelect.value = 'pterodactyl';
                        updateIntegrationFields();
                        return;
                    }
                    if (name.includes('виртуальные серверы') || name.includes('выделенные серверы')) {
                        integrationSelect.value = 'proxmoxve';
                        updateIntegrationFields();
                    }
                }

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

                    applyIntegrationTypeFromCategoryName(selectedOption ? selectedOption.textContent : '');
                }
                
                // Initialize on load
                document.addEventListener('DOMContentLoaded', function() {
                    const integrationSelect = document.getElementById('integration_type');
                    if (integrationSelect) {
                        integrationSelect.dataset.auto = "{{ $service->integration_type ? 'false' : 'true' }}";
                        integrationSelect.addEventListener('change', function() {
                            this.dataset.auto = 'false';
                        });
                    }

                    updateSubcategories();
                });
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

            <div class="mb-6">
                <label for="integration_type" class="block text-sm font-medium text-gray-300 mb-2">Категория интеграции</label>
                <select name="integration_type" id="integration_type" required
                        class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                        onchange="updateIntegrationFields()">
                    <option value="pterodactyl" {{ old('integration_type', $service->integration_type) === 'pterodactyl' ? 'selected' : '' }}>Pterodactyl Panel</option>
                    <option value="proxmoxve" {{ old('integration_type', $service->integration_type) === 'proxmoxve' ? 'selected' : '' }}>ProxmoxVE</option>
                    <option value="service" {{ old('integration_type', $service->integration_type) === 'service' ? 'selected' : '' }}>Услуга (вручную)</option>
                    <option value="other" {{ old('integration_type', $service->integration_type) === 'other' ? 'selected' : '' }}>Другое</option>
                </select>
                <x-input-error :messages="$errors->get('integration_type')" class="mt-2 text-red-400" />
            </div>

            <div class="mb-6 bg-[#0a0a0f] p-4 rounded-xl border border-white/10" id="pterodactyl_config" style="display: none;">
                <h3 class="text-lg font-semibold text-white mb-4">Настройки Pterodactyl</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Egg ID -->
                    <div>
                        <label for="pterodactyl_egg_id" class="block text-sm font-medium text-gray-400 mb-1">Egg ID</label>
                        <input type="number" name="pterodactyl[egg_id]" id="pterodactyl_egg_id" value="{{ old('pterodactyl.egg_id', $specifications['egg_id'] ?? '') }}"
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                        <p class="text-[10px] text-gray-500 mt-1">Остальные параметры (Nest, Image, Startup) подтянутся автоматически из Pterodactyl</p>
                    </div>
                    
                    <!-- Memory -->
                    <div>
                        <label for="pterodactyl_memory" class="block text-sm font-medium text-gray-400 mb-1">ОЗУ (Memory) MB</label>
                        <input type="number" name="pterodactyl[memory]" id="pterodactyl_memory" value="{{ old('pterodactyl.memory', $specifications['memory'] ?? 1024) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Disk -->
                    <div>
                        <label for="pterodactyl_disk" class="block text-sm font-medium text-gray-400 mb-1">Диск (Disk) MB</label>
                        <input type="number" name="pterodactyl[disk]" id="pterodactyl_disk" value="{{ old('pterodactyl.disk', $specifications['disk'] ?? 1024) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- CPU -->
                    <div>
                        <label for="pterodactyl_cpu" class="block text-sm font-medium text-gray-400 mb-1">CPU (%)</label>
                        <input type="number" name="pterodactyl[cpu]" id="pterodactyl_cpu" value="{{ old('pterodactyl.cpu', $specifications['cpu'] ?? 100) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Swap -->
                    <div>
                        <label for="pterodactyl_swap" class="block text-sm font-medium text-gray-400 mb-1">Swap (MB)</label>
                        <input type="number" name="pterodactyl[swap]" id="pterodactyl_swap" value="{{ old('pterodactyl.swap', $specifications['swap'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- IO -->
                    <div>
                        <label for="pterodactyl_io" class="block text-sm font-medium text-gray-400 mb-1">Block IO</label>
                        <input type="number" name="pterodactyl[io]" id="pterodactyl_io" value="{{ old('pterodactyl.io', $specifications['io'] ?? 500) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Databases -->
                    <div>
                        <label for="pterodactyl_databases" class="block text-sm font-medium text-gray-400 mb-1">Лимит баз данных</label>
                        <input type="number" name="pterodactyl[databases]" id="pterodactyl_databases" value="{{ old('pterodactyl.databases', $specifications['databases'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    
                    <!-- Backups -->
                    <div>
                        <label for="pterodactyl_backups" class="block text-sm font-medium text-gray-400 mb-1">Лимит бэкапов</label>
                        <input type="number" name="pterodactyl[backups]" id="pterodactyl_backups" value="{{ old('pterodactyl.backups', $specifications['backups'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                    <!-- Allocations -->
                    <div>
                        <label for="pterodactyl_allocations" class="block text-sm font-medium text-gray-400 mb-1">Доп. порты (Allocations)</label>
                        <input type="number" name="pterodactyl[allocations]" id="pterodactyl_allocations" value="{{ old('pterodactyl.allocations', $specifications['allocations'] ?? 0) }}" 
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>
                </div>
            </div>

            <div class="mb-6 bg-[#0a0a0f] p-4 rounded-xl border border-white/10" id="proxmox_config" style="display: none;">
                <h3 class="text-lg font-semibold text-white mb-4">Настройки ProxmoxVE</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="proxmox_node" class="block text-sm font-medium text-gray-400 mb-1">Node</label>
                        <input type="text" name="proxmox[node]" id="proxmox_node" value="{{ old('proxmox.node', $specifications['proxmox']['node'] ?? '') }}"
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>

                    <div>
                        <label for="proxmox_type" class="block text-sm font-medium text-gray-400 mb-1">Тип</label>
                        <select name="proxmox[type]" id="proxmox_type"
                                class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                            <option value="lxc" {{ old('proxmox.type', $specifications['proxmox']['type'] ?? 'lxc') === 'lxc' ? 'selected' : '' }}>LXC (контейнер)</option>
                            <option value="qemu" {{ old('proxmox.type', $specifications['proxmox']['type'] ?? '') === 'qemu' ? 'selected' : '' }}>QEMU (VM)</option>
                        </select>
                    </div>

                    <div>
                        <label for="proxmox_template_vmid" class="block text-sm font-medium text-gray-400 mb-1">Template VMID</label>
                        <input type="number" name="proxmox[template_vmid]" id="proxmox_template_vmid" value="{{ old('proxmox.template_vmid', $specifications['proxmox']['template_vmid'] ?? '') }}"
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>

                    <div>
                        <label for="proxmox_storage" class="block text-sm font-medium text-gray-400 mb-1">Storage (опционально)</label>
                        <input type="text" name="proxmox[storage]" id="proxmox_storage" value="{{ old('proxmox.storage', $specifications['proxmox']['storage'] ?? '') }}"
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>

                    <div>
                        <label for="proxmox_bridge" class="block text-sm font-medium text-gray-400 mb-1">Bridge (опционально)</label>
                        <input type="text" name="proxmox[bridge]" id="proxmox_bridge" value="{{ old('proxmox.bridge', $specifications['proxmox']['bridge'] ?? '') }}"
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>

                    <div>
                        <label for="proxmox_cores" class="block text-sm font-medium text-gray-400 mb-1">CPU (cores)</label>
                        <input type="number" name="proxmox[cores]" id="proxmox_cores" value="{{ old('proxmox.cores', $specifications['proxmox']['cores'] ?? '') }}"
                               class="w-full bg-[#0f0f13] border border-white/10 rounded-lg px-3 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    </div>

                    <div>
                        <label for="proxmox_memory_mb" class="block text-sm font-medium text-gray-400 mb-1">RAM (MB)</label>
                        <input type="number" name="proxmox[memory_mb]" id="proxmox_memory_mb" value="{{ old('proxmox.memory_mb', $specifications['proxmox']['memory_mb'] ?? '') }}"
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    function updateIntegrationFields() {
                        const integration = document.getElementById('integration_type');
                        const pterodactylConfig = document.getElementById('pterodactyl_config');
                        const proxmoxConfig = document.getElementById('proxmox_config');
                        const value = integration ? integration.value : 'other';

                        if (pterodactylConfig) pterodactylConfig.style.display = value === 'pterodactyl' ? 'block' : 'none';
                        if (proxmoxConfig) proxmoxConfig.style.display = value === 'proxmoxve' ? 'block' : 'none';
                    }

                    window.updateIntegrationFields = updateIntegrationFields;
                    updateIntegrationFields();
                });
            </script>

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
