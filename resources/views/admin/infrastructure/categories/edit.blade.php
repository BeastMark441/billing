<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Редактирование категории инфраструктуры</h1>
            <p class="text-gray-400">Измените параметры категории услуг</p>
        </div>
        <a href="{{ route('admin.infrastructure.categories.index') }}" class="text-gray-400 hover:text-white transition-colors">
            &larr; Назад к списку
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.infrastructure.categories.update', $category) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Название категории</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                       placeholder="Например: Вычисления">
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400" />
            </div>

            <!-- Slug -->
            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-300 mb-2">Slug (URL-идентификатор)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" 
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                       placeholder="Оставьте пустым для автоматического создания">
                <p class="text-xs text-gray-500 mt-1">Используется для формирования URL. Только латинские буквы, цифры и дефисы.</p>
                <x-input-error :messages="$errors->get('slug')" class="mt-2 text-red-400" />
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Описание</label>
                <textarea name="description" id="description" rows="3" 
                          class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                          placeholder="Краткое описание категории">{{ old('description', $category->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-400" />
            </div>

            <!-- Sort Order -->
            <div class="mb-4">
                <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">Порядок сортировки</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                       class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                <p class="text-xs text-gray-500 mt-1">Чем меньше число, тем выше категория в списке.</p>
                <x-input-error :messages="$errors->get('sort_order')" class="mt-2 text-red-400" />
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-300">Активная категория (видна пользователям)</span>
                </label>
                <x-input-error :messages="$errors->get('is_active')" class="mt-2 text-red-400" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.infrastructure.categories.index') }}" 
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
