<template>
    <div class="min-h-screen flex flex-col font-sans">
        <!-- Navigation -->
        <nav class="glass sticky top-0 z-50">
            <div class="container mx-auto px-6 py-4 flex items-center justify-between relative">
                <router-link to="/" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary to-green-600 rounded-lg flex items-center justify-center text-black font-bold text-lg shadow-[0_0_15px_rgba(166,203,64,0.3)] group-hover:shadow-[0_0_25px_rgba(166,203,64,0.5)] transition duration-300">
                        N
                    </div>
                    <span class="text-xl font-bold tracking-tight">Nodeum</span>
                </router-link>
                
                <!-- Guest Navigation -->
                <div v-if="!isAuthenticated" class="hidden md:flex items-center gap-8 text-sm font-medium absolute left-1/2 -translate-x-1/2">
                    <router-link
                        to="/"
                        :class="['text-gray-300 hover:text-white transition relative py-1 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full', $route.path === '/' ? 'text-white after:w-full' : '']"
                    >Главная</router-link>
                    <router-link
                        to="/services"
                        :class="['text-gray-300 hover:text-white transition relative py-1 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full', $route.path.startsWith('/services') ? 'text-white after:w-full' : '']"
                    >Услуги</router-link>
                    <router-link
                        to="/contacts"
                        :class="['text-gray-300 hover:text-white transition relative py-1 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full', $route.path.startsWith('/contacts') ? 'text-white after:w-full' : '']"
                    >Контакты</router-link>
                    <router-link
                        to="/catalog"
                        :class="['text-gray-300 hover:text-white transition relative py-1 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-primary after:transition-all hover:after:w-full', $route.path.startsWith('/catalog') ? 'text-white after:w-full' : '']"
                    >Каталог</router-link>
                </div>
                <div v-if="!isAuthenticated" class="hidden md:flex items-center gap-4 ml-auto">
                    <router-link to="/login" class="text-white hover:text-primary transition">Вход</router-link>
                    <router-link to="/register" class="btn-primary px-5 py-2 rounded-lg">Регистрация</router-link>
                </div>

                <!-- Authenticated Navigation -->
                <div v-else class="hidden md:flex items-center gap-2 text-sm font-medium flex-1">
                    <!-- Основные разделы -->
                    <div class="flex items-center gap-2 ml-6">
                        <router-link
                            to="/"
                            :class="['px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition border border-transparent hover:border-white/10', $route.path === '/' ? 'text-white bg-white/10 border-white/10' : '']"
                        >Главная</router-link>
                        <router-link
                            to="/services"
                            :class="['px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition border border-transparent hover:border-white/10', $route.path.startsWith('/services') ? 'text-white bg-white/10 border-white/10' : '']"
                        >Услуги</router-link>
                        <router-link
                            to="/contacts"
                            :class="['px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition border border-transparent hover:border-white/10', $route.path.startsWith('/contacts') ? 'text-white bg-white/10 border-white/10' : '']"
                        >Контакты</router-link>
                        <router-link
                            to="/products"
                            :class="['px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/5 transition border border-transparent hover:border-white/10', $route.path.startsWith('/products') ? 'text-white bg-white/10 border-white/10' : '']"
                        >Каталог</router-link>
                    </div>
                    <!-- Правый блок: кабинет/админ/выход -->
                    <div class="ml-auto flex items-center gap-2">
                        <router-link
                            to="/dashboard"
                            :class="['px-3 py-2 rounded-lg text-gray-200 hover:text-white transition', $route.path.startsWith('/dashboard') ? 'bg-primary/20 border border-primary/30' : 'bg-primary/10 border border-primary/30']"
                        >Личный кабинет</router-link>
                        <router-link
                            v-if="isAdmin"
                            to="/admin"
                            :class="['px-3 py-2 rounded-lg text-primary hover:text-white hover:bg-white/5 transition border border-transparent hover:border-white/10', $route.path.startsWith('/admin') ? 'text-white bg-white/10 border-white/10' : '']"
                        >Админ</router-link>
                        <div class="h-6 w-px bg-gray-700 mx-2"></div>
                        <button @click="logout" class="text-red-400 hover:text-red-300 transition flex items-center gap-2">
                            <span>Выйти</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-8 animate-[fade-in_0.5s_ease-out]">
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </main>
        
        <!-- Footer -->
        <footer class="border-t border-gray-800 bg-black/40 backdrop-blur-sm mt-auto">
            <div class="container mx-auto px-6 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 bg-primary rounded flex items-center justify-center text-black font-bold text-xs">N</div>
                            <span class="font-bold text-lg">Nodeum</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Премиальный игровой хостинг с защитой от DDoS и мгновенной активацией.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4 text-white">Услуги</h4>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li><router-link to="/services" class="hover:text-primary transition">Minecraft Hosting</router-link></li>
                            <li><router-link to="/services" class="hover:text-primary transition">Rust Hosting</router-link></li>
                            <li><router-link to="/services" class="hover:text-primary transition">VPS / VDS</router-link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4 text-white">Компания</h4>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li><router-link to="/legal" class="hover:text-primary transition">Оферта</router-link></li>
                            <li><router-link to="/legal" class="hover:text-primary transition">Конфиденциальность</router-link></li>
                            <li><router-link to="/contacts" class="hover:text-primary transition">Контакты</router-link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4 text-white">Поддержка</h4>
                        <p class="text-gray-500 text-sm mb-2">24/7 Онлайн поддержка</p>
                        <a href="mailto:support@nodeum.ru" class="text-primary hover:underline">support@nodeum.ru</a>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-center text-gray-600 text-xs">
                    <p>&copy; 2026 Nodeum Hosting. Все права защищены.</p>
                </div>
            </div>
        </footer>
        
        <div v-if="alertVisible" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div class="glass-card rounded-2xl w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Уведомление</h3>
                    <button class="text-gray-400 hover:text-white" @click="closeAlert">✕</button>
                </div>
                <div class="text-sm text-gray-200 whitespace-pre-line">
                    {{ alertMessage }}
                </div>
                <div class="text-right mt-6">
                    <button class="btn-primary px-5 py-2 rounded-lg" @click="closeAlert">Понятно</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        isAuthenticated() {
            return !!localStorage.getItem('token');
        },
        isAdmin() {
            return localStorage.getItem('role') === 'admin';
        }
    },
    data() {
        return {
            alertVisible: false,
            alertMessage: ''
        }
    },
    methods: {
        translateMessage(msg) {
            if (!msg) return '';
            const map = [
                { k: 'Error creating node', v: 'Не удалось создать узел' },
                { k: 'Error creating product', v: 'Не удалось создать тариф' },
                { k: 'Error deleting', v: 'Не удалось удалить' },
                { k: 'Update failed', v: 'Не удалось обновить' },
                { k: 'Delete failed', v: 'Не удалось удалить' },
                { k: 'Reply failed', v: 'Не удалось отправить ответ' },
                { k: 'Error loading ticket', v: 'Ошибка загрузки тикета' },
                { k: 'Power action failed', v: 'Ошибка операции питания' },
                { k: 'Signal', v: 'Сигнал' }
            ];
            for (const m of map) {
                if (msg.includes(m.k)) {
                    return msg.replace(m.k, m.v);
                }
            }
            return msg;
        },
        showAlert(message) {
            this.alertMessage = this.translateMessage(String(message || ''));
            this.alertVisible = true;
        },
        closeAlert() {
            this.alertVisible = false;
            this.alertMessage = '';
        },
        logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('role');
            this.$router.push('/login');
            window.location.reload();
        }
    },
    mounted() {
        const self = this;
        window.alert = function(message) { self.showAlert(message); };
    }
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
