<div
    x-data="{
        open: false,
        init() {
            this.open = !document.cookie.split('; ').some(v => v.startsWith('nodeum_cookie_consent='))
        },
        accept() {
            const days = 365
            const expires = new Date(Date.now() + days * 864e5).toUTCString()
            document.cookie = 'nodeum_cookie_consent=1; expires=' + expires + '; path=/; samesite=lax'
            this.open = false
        }
    }"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed bottom-4 left-4 right-4 z-50"
    style="display: none;"
>
    <div class="max-w-5xl mx-auto bg-[#050508] border border-white/10 rounded-2xl px-5 py-4 shadow-2xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="text-sm text-gray-300">
                Мы используем cookies для работы сайта, аналитики и улучшения качества сервиса.
                <a href="{{ route('legal.doc', 'cookies') }}" class="text-[#a6cb40] hover:underline">Подробнее</a>
            </div>
            <div class="flex gap-3">
                <button @click="accept" class="px-4 py-2 rounded-xl bg-[#a6cb40] hover:bg-[#8eb330] text-[#0a0a0f] font-bold transition-colors">Принять</button>
                <a href="{{ route('legal') }}" class="px-4 py-2 rounded-xl border border-white/15 hover:bg-white/5 text-white font-semibold transition-colors">Документы</a>
            </div>
        </div>
    </div>
</div>

