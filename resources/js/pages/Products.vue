<template>
    <div class="space-y-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-white mb-4">
                Выберите свой тариф
            </h1>
            <p class="text-gray-400 max-w-2xl mx-auto">
                Мощные игровые серверы с защитой от DDoS и мгновенной установкой.
            </p>
        </div>
        
        <div v-if="products.length === 0" class="text-center py-20 text-gray-500">
            Загрузка тарифов...
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="product in visibleProducts" :key="product.id" class="glass-card p-1 rounded-2xl group hover:-translate-y-2 transition duration-300">
                <div class="bg-[#0a0a0f] p-6 rounded-xl h-full flex flex-col relative overflow-hidden">
                    <!-- Glow effect -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition"></div>

                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-white mb-2">{{ product.name }}</h2>
                        <div class="text-xs text-gray-400 uppercase tracking-wider">{{ product.category?.name || product.type }}</div>
                    </div>

                    <div class="text-4xl font-bold text-white mb-2 flex items-baseline gap-1">
                        {{ product.price_monthly }}₽
                        <span class="text-sm text-gray-500 font-normal">/ месяц</span>
                    </div>
                    <div v-if="product.trials && product.trials.length && product.trials[0].active" class="mb-4">
                        <span class="text-xs px-2 py-1 rounded bg-primary/10 text-primary border border-primary/30">
                            Доступен триал: {{ product.trials[0].duration_days }} дней
                        </span>
                    </div>

                    <p class="text-gray-400 mb-6 text-sm h-12 line-clamp-2">
                        {{ product.description || 'Идеально для начала игры с друзьями' }}
                    </p>
                    
                    <ul class="space-y-3 mb-8 text-sm text-gray-300 flex-grow">
                        <li class="flex items-center gap-3">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                            <span>CPU: <b class="text-white">{{ product.resources.cpu }}%</b></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                            <span>RAM: <b class="text-white">{{ product.resources.ram }} MB</b></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                            <span>Disk: <b class="text-white">{{ product.resources.disk }} MB</b></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-primary"></span>
                            <span>Ports: <b class="text-white">{{ product.resources.ports }}</b></span>
                        </li>
                    </ul>
                    
                    <div class="grid grid-cols-2 gap-3 mt-auto">
                        <button @click="openOrder(product)" :disabled="processing" 
                        class="w-full btn-primary py-3 rounded-xl font-bold flex items-center justify-center gap-2 group-hover:shadow-[0_0_20px_rgba(166,203,64,0.4)] transition">
                            <span v-if="!processing">Заказать</span>
                            <span v-if="!processing">→</span>
                            <span v-else>Обработка...</span>
                        </button>
                        <button v-if="product.trials && product.trials.length && product.trials[0].active"
                            @click="startTrial(product)" :disabled="processing"
                            class="w-full px-4 py-3 rounded-xl border border-white/10 text-gray-200 hover:bg-white/5">
                            Пробный период
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="orderModalVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div class="glass-card rounded-2xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Оформление заказа</h3>
                    <button class="text-gray-400 hover:text-white" @click="closeOrder">✕</button>
                </div>
                <div class="space-y-4">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <div class="font-bold">{{ selectedProduct?.name }}</div>
                        <div class="text-sm text-gray-400 mt-1">{{ selectedProduct?.price_monthly }}₽ / месяц</div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Промокод</label>
                        <input v-model="couponCode" placeholder="Введите промокод" class="input-field w-full px-4 py-3 rounded-xl text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Способ оплаты</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="payMethod = 'balance'" :class="['px-4 py-3 rounded-xl border', payMethod==='balance' ? 'border-primary bg-primary/10 text-white' : 'border-white/10 text-gray-300 hover:bg-white/5']">Баланс</button>
                            <button @click="payMethod = 'gateway'" :class="['px-4 py-3 rounded-xl border', payMethod==='gateway' ? 'border-primary bg-primary/10 text-white' : 'border-white/10 text-gray-300 hover:bg-white/5']">Платёжная система</button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <button @click="addToCart" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-sm">В корзину</button>
                        <div class="flex gap-3">
                            <button @click="closeOrder" class="text-gray-400">Отмена</button>
                            <button @click="submitOrder" :disabled="processing" class="btn-primary px-6 py-2 rounded-lg">
                                <span v-if="!processing">Оплатить</span>
                                <span v-else>Обработка...</span>
                            </button>
                        </div>
                    </div>
                    <div v-if="orderError" class="text-red-400 text-sm">{{ orderError }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            products: [],
            processing: false,
            orderModalVisible: false,
            selectedProduct: null,
            couponCode: '',
            payMethod: 'balance',
            orderError: ''
        }
    },
    computed: {
        isAuthenticated() {
            return !!localStorage.getItem('token');
        },
        visibleProducts() {
            const items = this.products.filter(p => !p.is_hidden && (!p.category || p.category.is_visible));
            const slug = this.$route.query.category;
            if (slug) {
                return items.filter(p => p.category && p.category.slug === slug);
            }
            return items;
        }
    },
    async mounted() {
        try {
            const response = await axios.get('/products');
            this.products = response.data;
        } catch (error) {
            console.error(error);
        }
    },
    methods: {
        openOrder(product) {
            if (!this.isAuthenticated) {
                this.$router.push({ path: '/login', query: { next: this.$route.fullPath } });
                return;
            }
            this.selectedProduct = product;
            this.couponCode = '';
            this.payMethod = 'balance';
            this.orderError = '';
            this.orderModalVisible = true;
        },
        async startTrial(product) {
            if (!this.isAuthenticated) {
                this.$router.push({ path: '/login', query: { next: this.$route.fullPath } });
                return;
            }
            this.processing = true;
            this.orderError = '';
            try {
                await axios.post('/client/orders', { product_id: product.id, use_trial: true });
                this.$router.push('/dashboard');
            } catch (error) {
                this.orderError = 'Ошибка заказа: ' + (error.response?.data?.error || error.message);
            } finally {
                this.processing = false;
            }
        },
        closeOrder() {
            this.orderModalVisible = false;
            this.selectedProduct = null;
            this.orderError = '';
        },
        addToCart() {
            if (!this.isAuthenticated) {
                this.$router.push({ path: '/login', query: { next: this.$route.fullPath } });
                return;
            }
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            if (this.selectedProduct) {
                cart.push({ id: this.selectedProduct.id, coupon: this.couponCode || null });
                localStorage.setItem('cart', JSON.stringify(cart));
                this.closeOrder();
            }
        },
        async submitOrder() {
            if (!this.isAuthenticated) {
                this.$router.push({ path: '/login', query: { next: this.$route.fullPath } });
                return;
            }
            if (!this.selectedProduct) return;
            this.processing = true;
            this.orderError = '';
            try {
                await axios.post('/client/orders', { product_id: this.selectedProduct.id, coupon: this.couponCode || undefined, pay_method: this.payMethod });
                this.closeOrder();
                this.$router.push('/dashboard');
            } catch (error) {
                this.orderError = 'Ошибка заказа: ' + (error.response?.data?.error || error.message);
            } finally {
                this.processing = false;
            }
        },
    }
}
</script>
