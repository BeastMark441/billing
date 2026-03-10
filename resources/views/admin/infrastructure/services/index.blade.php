<x-admin-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Услуги инфраструктуры</h1>
            <p class="text-gray-400">Управление тарифами и услугами</p>
        </div>
        <a href="{{ route('admin.infrastructure.services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            Добавить услугу
        </a>
    </div>

    <div class="bg-[#0f0f13] border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Категория</th>
                        <th class="px-6 py-4 font-medium">Название</th>
                        <th class="px-6 py-4 font-medium">Цена</th>
                        <th class="px-6 py-4 font-medium">Сортировка</th>
                        <th class="px-6 py-4 font-medium">Статус</th>
                        <th class="px-6 py-4 font-medium text-right">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($services as $service)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-gray-300 font-medium">
                            {{ $service->category->name }}
                        </td>
                        <td class="px-6 py-4 text-white font-medium">
                            {{ $service->name }}
                            <div class="text-xs text-gray-500 font-mono">{{ $service->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-white font-mono">
                            {{ number_format($service->price, 2) }} ₽
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            {{ $service->sort_order }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-500/10 text-green-500' : 'bg-gray-500/10 text-gray-500' }}">
                                {{ $service->is_active ? 'Активна' : 'Неактивна' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.infrastructure.services.edit', $service) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition-colors">
                                    Редактировать
                                </a>
                                <form action="{{ route('admin.infrastructure.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Удалить услугу?');" class="inline">
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
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Услуги не найдены
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
