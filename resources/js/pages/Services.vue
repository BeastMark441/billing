<template>
    <div class="container mx-auto px-4 py-12">
        
        <h1 class="text-4xl font-bold mb-8 text-center">Наши Услуги</h1>
        
        <!-- Categories list -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold mb-6 border-l-4 border-primary pl-4">Категории</h2>
            <div v-if="categories.length === 0" class="text-gray-500">Загрузка...</div>
            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div v-for="cat in categories" :key="cat.id" class="bg-gray-900 rounded-xl p-6 border border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img v-if="cat.image_url" :src="cat.image_url" alt="" class="w-16 h-16 rounded-lg object-cover border border-gray-800">
                        <div>
                            <div class="font-bold text-lg">{{ cat.name }}</div>
                            <div class="text-gray-400 text-sm" v-if="cat.description">{{ cat.description }}</div>
                        </div>
                    </div>
                    <router-link :to="{ path: '/catalog', query: { category: cat.slug } }" class="px-4 py-2 rounded-lg bg-primary text-black font-bold hover:opacity-90 transition">
                        Смотреть
                    </router-link>
                </div>
            </div>
        </section>

        <!-- VPS / VDS -->
        <section>
            <h2 class="text-2xl font-bold mb-6 border-l-4 border-primary pl-4">VPS / VDS</h2>
            <div class="bg-gray-900 rounded-xl p-8 border border-gray-800 space-y-6">
                <p class="text-gray-300">
                    Под ваш запрос подготовим индивидуальный тариф для любого сервера. Оставьте заявку — мы подскажем конфигурацию и стоимость.
                </p>
                <form @submit.prevent="submitVpsRequest" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-400">Тема заявки</label>
                        <input v-model="vpsForm.subject" required placeholder="Например: Индивидуальный тариф VPS для бота" class="input-field w-full px-4 py-2 rounded-lg text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-400">Описание</label>
                        <textarea v-model="vpsForm.message" required class="input-field w-full px-4 py-2 rounded-lg text-white h-32" placeholder="Кратко опишите требования: CPU, RAM, диски, трафик, регион..."></textarea>
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Отправить заявку</button>
                    </div>
                </form>
                <div v-if="vpsNotice" class="text-sm text-gray-400">{{ vpsNotice }}</div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    name: 'Services',
    data() {
        return {
            products: [],
            vpsForm: {
                subject: '',
                message: ''
            },
            vpsNotice: ''
        }
    },
    computed: {
        categories() {
            const map = new Map();
            for (const p of this.products) {
                if (p.category && p.category.is_visible && !p.is_hidden) {
                    if (!map.has(p.category.slug)) {
                        map.set(p.category.slug, p.category);
                    }
                }
            }
            return Array.from(map.values());
        }
    },
    async mounted() {
        try {
            const res = await axios.get('/products');
            this.products = res.data;
        } catch (e) {
            console.error(e);
        }
    },
    methods: {
        async submitVpsRequest() {
            this.vpsNotice = '';
            try {
                await axios.post('/client/tickets', {
                    subject: this.vpsForm.subject,
                    message: this.vpsForm.message,
                    category: 'vps_custom',
                    priority: 'medium'
                });
                this.vpsForm.subject = '';
                this.vpsForm.message = '';
                this.vpsNotice = 'Заявка отправлена. Мы свяжемся с вами в ближайшее время.';
            } catch (e) {
                if (e.response && e.response.status === 401) {
                    this.vpsNotice = 'Для отправки заявки войдите в аккаунт.';
                } else {
                    this.vpsNotice = 'Не удалось отправить заявку. Попробуйте позже.';
                }
            }
        }
    }
}
</script>
