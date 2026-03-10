@extends('layouts.landing')

@section('content')
<!-- Hero Section -->
<section class="relative py-24 md:py-36 overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-[600px] h-[600px] bg-[#a6cb40]/10 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-[400px] h-[400px] bg-[#a6cb40]/5 rounded-full blur-[100px]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6">
                Ваша инфраструктура в <span class="text-[#a6cb40]">надёжных руках</span>
            </h1>
            <p class="text-xl text-gray-400 mb-10 leading-relaxed">
                От игровых серверов до мощных VDS и облачных решений. NODEUM — это профессиональный хостинг с низким пингом, высокой доступностью и мгновенной поддержкой.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#" class="bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] px-8 py-4 rounded-lg font-bold text-lg transition-all shadow-[0_0_20px_rgba(166,203,64,0.3)]">
                    Выбрать решение
                </a>
                <a href="#" class="border border-white/20 hover:border-[#a6cb40]/50 hover:bg-white/5 text-white px-8 py-4 rounded-lg font-bold text-lg transition-all">
                    Посмотреть цены
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="py-20 bg-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-16">
            <h2 class="text-3xl font-bold mb-4">Наши решения</h2>
            <div class="h-1 w-20 bg-[#a6cb40]"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- VDS/VPS -->
            <div class="group bg-[#0a0a0f] border border-white/10 p-8 rounded-2xl hover:border-[#a6cb40]/50 transition-all hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                <div class="w-12 h-12 bg-[#a6cb40]/10 rounded-lg flex items-center justify-center text-[#a6cb40] mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">VDS / VPS</h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                    Высокопроизводительные виртуальные серверы на NVMe дисках. Полный root-доступ и гибкая настройка под ваши задачи.
                </p>
                <a href="#" class="text-[#a6cb40] font-semibold text-sm flex items-center gap-2 group/link">
                    Подробнее 
                    <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Game Hosting -->
            <div class="group bg-[#0a0a0f] border border-white/10 p-8 rounded-2xl hover:border-[#a6cb40]/50 transition-all hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                <div class="w-12 h-12 bg-[#a6cb40]/10 rounded-lg flex items-center justify-center text-[#a6cb40] mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Игровые решения</h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                    Специализированный хостинг для Minecraft, CS2, Rust и других игр. Низкий пинг, защита от DDoS и удобная панель.
                </p>
                <a href="#" class="text-[#a6cb40] font-semibold text-sm flex items-center gap-2 group/link">
                    Подробнее 
                    <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Web Hosting -->
            <div class="group bg-[#0a0a0f] border border-white/10 p-8 rounded-2xl hover:border-[#a6cb40]/50 transition-all hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                <div class="w-12 h-12 bg-[#a6cb40]/10 rounded-lg flex items-center justify-center text-[#a6cb40] mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Хостинг сайтов</h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                    Быстрый хостинг для ваших сайтов на PHP/Node.js. Бесплатные SSL, автоматические бэкапы и удобная установка CMS.
                </p>
                <a href="#" class="text-[#a6cb40] font-semibold text-sm flex items-center gap-2 group/link">
                    Подробнее 
                    <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Databases -->
            <div class="group bg-[#0a0a0f] border border-white/10 p-8 rounded-2xl hover:border-[#a6cb40]/50 transition-all hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                <div class="w-12 h-12 bg-[#a6cb40]/10 rounded-lg flex items-center justify-center text-[#a6cb40] mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Базы данных</h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                    Управляемые базы данных MySQL, PostgreSQL, Redis. Гарантированная сохранность данных и высокая скорость работы.
                </p>
                <a href="#" class="text-[#a6cb40] font-semibold text-sm flex items-center gap-2 group/link">
                    Подробнее 
                    <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Voice Servers -->
            <div class="group bg-[#0a0a0f] border border-white/10 p-8 rounded-2xl hover:border-[#a6cb40]/50 transition-all hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                <div class="w-12 h-12 bg-[#a6cb40]/10 rounded-lg flex items-center justify-center text-[#a6cb40] mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Голосовые серверы</h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                    Качественные серверы TeamSpeak 3 и Mumble. Стабильная связь без задержек для вашей команды или сообщества.
                </p>
                <a href="#" class="text-[#a6cb40] font-semibold text-sm flex items-center gap-2 group/link">
                    Подробнее 
                    <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Mail Servers -->
            <div class="group bg-[#0a0a0f] border border-white/10 p-8 rounded-2xl hover:border-[#a6cb40]/50 transition-all hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                <div class="w-12 h-12 bg-[#a6cb40]/10 rounded-lg flex items-center justify-center text-[#a6cb40] mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Корпоративная почта</h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                    Профессиональные почтовые ящики на вашем домене. Защита от спама, веб-интерфейс и поддержка IMAP/SMTP.
                </p>
                <a href="#" class="text-[#a6cb40] font-semibold text-sm flex items-center gap-2 group/link">
                    Подробнее 
                    <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-4xl font-bold mb-8 leading-tight">Почему выбирают <span class="text-[#a6cb40]">NODEUM</span>?</h2>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-[#a6cb40]/10 rounded-full flex items-center justify-center text-[#a6cb40]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold mb-1">Мгновенная активация</h4>
                            <p class="text-gray-400 text-sm">Ваш сервер будет готов к работе через несколько минут после оплаты.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-[#a6cb40]/10 rounded-full flex items-center justify-center text-[#a6cb40]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold mb-1">DDoS Защита</h4>
                            <p class="text-gray-400 text-sm">Все наши сервисы защищены мощными фильтрами для обеспечения 99.9% аптайма.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-[#a6cb40]/10 rounded-full flex items-center justify-center text-[#a6cb40]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold mb-1">Поддержка 24/7</h4>
                            <p class="text-gray-400 text-sm">Наша команда экспертов всегда готова помочь вам с любыми вопросами.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-[#a6cb40]/20 rounded-3xl blur-2xl"></div>
                <div class="relative bg-gradient-to-br from-white/10 to-transparent border border-white/10 p-2 rounded-3xl">
                    <img src="https://coreva-normal.trae.ai/api/ide/v1/text_to_image?prompt=Modern+server+rack+in+a+dark+data+center+with+green+neon+lights+cyberpunk+style+high+tech+photography&image_size=landscape_16_9" alt="Server Infrastructure" class="rounded-2xl shadow-2xl">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-[#a6cb40] to-[#8eb330] rounded-[2rem] p-12 text-center text-[#0a0a0f]">
            <h2 class="text-4xl font-bold mb-6">Готовы начать?</h2>
            <p class="text-lg font-medium mb-10 opacity-90 max-w-2xl mx-auto">
                Запустите свой проект на профессиональном оборудовании уже сегодня. 
                Простая настройка и выгодные тарифы ждут вас.
            </p>
            <a href="#" class="bg-[#0a0a0f] text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-black transition-all shadow-xl">
                Создать аккаунт
            </a>
        </div>
    </div>
</section>

@endsection
