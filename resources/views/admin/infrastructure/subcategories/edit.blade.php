<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Редактирование подкатегории</h1>
            <p class="text-gray-400">Измените параметры подкатегории</p>
        </div>
        <a href="{{ route('admin.infrastructure.subcategories.index') }}" class="text-gray-400 hover:text-white transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.infrastructure.subcategories.update', $subcategory) }}">
            @csrf
            @method('PUT')

            <!-- Category -->
            <div class="mb-4">
                <label for="infrastructure_category_id" class="block text-sm font-medium text-gray-300 mb-2">Родительская категория</label>
                <select name="infrastructure_category_id" id="infrastructure_category_id" required 
                        class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                    <option value="">Выберите категорию</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('infrastructure_category_id', $subcategory->infrastructure_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('infrastructure_category_id')" class="mt-2 text-red-400" />
            </div>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Название подкатегории</label>
                <input type="text" name="name" id="name" value="{{ old('name', $subcategory->name) }}" required 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                       placeholder="Например: VPS">
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400" />
            </div>

            <!-- Slug -->
            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-300 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $subcategory->slug) }}" 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                       placeholder="Автоматически из названия">
                <x-input-error :messages="$errors->get('slug')" class="mt-2 text-red-400" />
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Описание</label>
                <textarea name="description" id="description" rows="3" 
                          class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                          placeholder="Описание подкатегории">{{ old('description', $subcategory->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-400" />
            </div>

            <!-- Sort Order -->
            <div class="mb-4">
                <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">Порядок сортировки</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $subcategory->sort_order) }}" min="0"
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                <x-input-error :messages="$errors->get('sort_order')" class="mt-2 text-red-400" />
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subcategory->is_active) ? 'checked' : '' }} 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-300">Активная подкатегория</span>
                </label>
                <x-input-error :messages="$errors->get('is_active')" class="mt-2 text-red-400" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.infrastructure.subcategories.index') }}" 
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
