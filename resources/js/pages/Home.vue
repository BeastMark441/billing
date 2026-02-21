<template>
    <div class="space-y-32">
        <!-- Hero Section -->
        <section class="relative text-center py-20 lg:py-32">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/10 via-transparent to-transparent opacity-50 blur-3xl pointer-events-none"></div>
            
            <h1 class="text-5xl lg:text-7xl font-extrabold mb-8 tracking-tight">
                Игровой хостинг <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-green-400">нового поколения</span>
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Nodeum предоставляет высокопроизводительные серверы на базе Ryzen 9 7950X.
                Мгновенная установка, Anti-DDoS Game protection и поддержка 24/7.
            </p>
            <div class="flex justify-center gap-6">
                <router-link to="/services" class="btn-primary px-8 py-4 rounded-xl text-lg font-bold shadow-lg shadow-primary/20 hover:shadow-primary/40 transition transform hover:-translate-y-1">
                    Выбрать тариф
                </router-link>
                <router-link to="/contacts" class="glass px-8 py-4 rounded-xl text-lg font-bold hover:bg-white/10 transition transform hover:-translate-y-1 border border-white/10">
                    Связаться с нами
                </router-link>
            </div>
        </section>

        <!-- Features -->
        <section class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center mb-6 border border-white/5 group-hover:border-primary/50 transition duration-300">
                        <span class="text-3xl filter drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">⚡</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition">Мгновенная активация</h3>
                    <p class="text-gray-400 leading-relaxed">Автоматическая система установки развернет ваш сервер за 60 секунд после оплаты. Начинайте играть сразу.</p>
                </div>
                <div class="glass-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center mb-6 border border-white/5 group-hover:border-primary/50 transition duration-300">
                        <span class="text-3xl filter drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">🛡️</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition">DDoS Защита</h3>
                    <p class="text-gray-400 leading-relaxed">Собственная система фильтрации трафика на уровне L3/L4/L7 защищает от атак любой сложности без повышения пинга.</p>
                </div>
                <div class="glass-card p-8 rounded-2xl group">
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center mb-6 border border-white/5 group-hover:border-primary/50 transition duration-300">
                        <span class="text-3xl filter drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]">💎</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 group-hover:text-primary transition">Премиум железо</h3>
                    <p class="text-gray-400 leading-relaxed">Мы используем только флагманские процессоры AMD Ryzen 9 7950X и быстрые NVMe Gen4 накопители.</p>
                </div>
            </div>
        </section>

        <!-- Stats / Trust -->
        <section class="border-y border-white/5 bg-white/[0.02] py-16">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-4xl font-bold text-white mb-2">99.9%</div>
                        <div class="text-primary text-sm uppercase tracking-wider font-semibold">Uptime</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-white mb-2">10Gbps</div>
                        <div class="text-primary text-sm uppercase tracking-wider font-semibold">Канал</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-white mb-2">24/7</div>
                        <div class="text-primary text-sm uppercase tracking-wider font-semibold">Поддержка</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-white mb-2">1000+</div>
                        <div class="text-primary text-sm uppercase tracking-wider font-semibold">Клиентов</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Plans Preview -->
        <section class="container mx-auto px-4 text-center pb-20">
            <h2 class="text-4xl font-bold mb-4">Популярные тарифы</h2>
            <p class="text-gray-400 mb-12">Выберите идеальное решение для вашего проекта</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Start Plan -->
                <div class="glass-card p-1 rounded-3xl hover:scale-105 transition duration-300">
                    <div class="bg-[#13131b] rounded-[22px] p-8 h-full flex flex-col relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-gray-700 to-transparent"></div>
                        <h3 class="text-2xl font-bold mb-2">Start</h3>
                        <p class="text-gray-500 text-sm mb-6">Для небольших проектов</p>
                        <div class="text-4xl font-bold text-white mb-8">299₽<span class="text-lg text-gray-500 font-normal">/мес</span></div>
                        
                        <ul class="text-left space-y-4 mb-8 text-gray-300 flex-grow">
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 1 vCore Ryzen 9</li>
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 2 GB DDR5 RAM</li>
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 20 GB NVMe SSD</li>
                        </ul>
                        <router-link to="/register" class="w-full glass py-3 rounded-xl font-bold hover:bg-white/10 transition border border-white/10 block">Заказать</router-link>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="p-[2px] rounded-3xl bg-gradient-to-b from-primary to-green-700 shadow-[0_0_40px_-10px_rgba(166,203,64,0.3)] hover:scale-105 transition duration-300 relative z-10">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-primary to-green-600 text-black text-xs font-bold px-4 py-1 rounded-full shadow-lg">ХИТ ПРОДАЖ</div>
                    <div class="bg-[#13131b] rounded-[22px] p-8 h-full flex flex-col">
                        <h3 class="text-2xl font-bold mb-2 text-primary">Pro</h3>
                        <p class="text-gray-400 text-sm mb-6">Золотая середина</p>
                        <div class="text-5xl font-bold text-white mb-8">599₽<span class="text-lg text-gray-500 font-normal">/мес</span></div>
                        
                        <ul class="text-left space-y-4 mb-8 text-white flex-grow">
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 2 vCore Ryzen 9</li>
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 4 GB DDR5 RAM</li>
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 40 GB NVMe SSD</li>
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> Приоритетная поддержка</li>
                        </ul>
                        <router-link to="/register" class="w-full btn-primary py-4 rounded-xl font-bold block shadow-lg">Заказать сейчас</router-link>
                    </div>
                </div>

                <!-- Ultra Plan -->
                <div class="glass-card p-1 rounded-3xl hover:scale-105 transition duration-300">
                    <div class="bg-[#13131b] rounded-[22px] p-8 h-full flex flex-col relative overflow-hidden">
                         <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent"></div>
                        <h3 class="text-2xl font-bold mb-2">Ultra</h3>
                        <p class="text-gray-500 text-sm mb-6">Максимальная мощь</p>
                        <div class="text-4xl font-bold text-white mb-8">1199₽<span class="text-lg text-gray-500 font-normal">/мес</span></div>
                        
                        <ul class="text-left space-y-4 mb-8 text-gray-300 flex-grow">
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 4 vCore Ryzen 9</li>
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 8 GB DDR5 RAM</li>
                            <li class="flex items-center gap-3"><span class="text-primary">✓</span> 80 GB NVMe SSD</li>
                        </ul>
                        <router-link to="/register" class="w-full glass py-3 rounded-xl font-bold hover:bg-white/10 transition border border-white/10 block">Заказать</router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: 'Home'
}
</script>
