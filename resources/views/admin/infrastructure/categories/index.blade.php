<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Категории инфраструктуры</h1>
            <p class="text-gray-400">Управление категориями услуг</p>
        </div>
        <a href="{{ route('admin.infrastructure.categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            Добавить категорию
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">ID</th>
                        <th class="px-6 py-4 font-medium">Название</th>
                        <th class="px-6 py-4 font-medium">Slug</th>
                        <th class="px-6 py-4 font-medium">Описание</th>
                        <th class="px-6 py-4 font-medium">Сортировка</th>
                        <th class="px-6 py-4 font-medium">Статус</th>
                        <th class="px-6 py-4 font-medium text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($categories as $category)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                            #{{ $category->id }}
                        </td>
                        <td class="px-6 py-4 text-white font-medium">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4 text-gray-400 font-mono text-sm">
                            {{ $category->slug }}
                        </td>
                        <td class="px-6 py-4 text-gray-300 text-sm">
                            {{ $category->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            {{ $category->sort_order }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-500/10 text-green-500' : 'bg-gray-500/10 text-gray-500' }}">
                                {{ $category->is_active ? 'Активна' : 'Неактивна' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.infrastructure.services.index', ['infrastructure_category_id' => $category->id]) }}" class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white rounded text-xs font-medium transition-colors">
                                    Услуги ({{ $category->services_count ?? 0 }})
                                </a>
                                <a href="{{ route('admin.infrastructure.categories.edit', $category) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition-colors">
                                    Редактировать
                                </a>
                                <form action="{{ route('admin.infrastructure.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Удалить категорию?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition-colors">
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Категории не найдены
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
