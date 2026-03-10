<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Статус систем</h2>
            <p class="text-sm text-gray-400">Информация о доступности сервисов NODEUM.</p>
        </div>

        <div class="bg-[#050508] border border-white/10 rounded-xl p-6">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Все системы работают штатно</h3>
                    <p class="text-gray-400">Последнее обновление: {{ now()->format('d.m.Y H:i') }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <!-- System Item -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="font-medium text-white">Личный кабинет</span>
                    </div>
                    <span class="text-sm text-green-500 font-medium">Работает</span>
                </div>

                <!-- System Item -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="font-medium text-white">VDS Nodes (RU-MSK)</span>
                    </div>
                    <span class="text-sm text-green-500 font-medium">Работает</span>
                </div>

                <!-- System Item -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="font-medium text-white">Game Hosting Nodes</span>
                    </div>
                    <span class="text-sm text-green-500 font-medium">Работает</span>
                </div>

                <!-- System Item -->
                <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="font-medium text-white">API Gateway</span>
                    </div>
                    <span class="text-sm text-green-500 font-medium">Работает</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
