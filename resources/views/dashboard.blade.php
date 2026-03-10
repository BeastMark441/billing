<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Панель управления') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#0a0a0f] border border-white/10 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    {{ __("Вы успешно вошли в систему! Панель управления находится в разработке.") }}
                    <div class="mt-4">
                        <a href="/" class="text-[#a6cb40] hover:underline">Вернуться на главную</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
