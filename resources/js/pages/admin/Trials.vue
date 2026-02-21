<template>
    <div class="space-y-8">
        <h1 class="text-3xl font-bold">Триальные периоды</h1>
        
        <div class="glass-card p-6 rounded-2xl">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <router-link to="/admin" class="text-gray-400 hover:text-white text-sm">← Назад</router-link>
                    <input v-model="search" placeholder="Поиск по продукту..." class="input-field w-full md:w-80 px-4 py-2 rounded-lg text-white">
                </div>
                <button @click="openCreate" class="btn-primary px-4 py-2 rounded-lg text-sm shadow-lg">+ Триал</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 text-sm border-b border-white/10">
                            <th class="p-4">ID</th>
                            <th class="p-4">Продукт</th>
                            <th class="p-4">Длительность (дней)</th>
                            <th class="p-4">Макс. на пользователя</th>
                            <th class="p-4">Статус</th>
                            <th class="p-4">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in filteredTrials" :key="t.id" class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500">#{{ t.id }}</td>
                            <td class="p-4">{{ t.product?.name }}</td>
                            <td class="p-4">{{ t.duration_days }}</td>
                            <td class="p-4">{{ t.max_per_user }}</td>
                            <td class="p-4">
                                <span :class="t.active ? 'text-green-400 bg-green-400/10' : 'text-red-400 bg-red-400/10'" class="px-2 py-1 rounded text-xs uppercase font-bold">
                                    {{ t.active ? 'active' : 'inactive' }}
                                </span>
                            </td>
                            <td class="p-4 flex gap-2">
                                <button @click="openEdit(t)" class="text-blue-400 hover:text-white text-xs bg-blue-400/10 px-2 py-1 rounded">Редактировать</button>
                                <button @click="remove(t)" class="text-red-400 hover:text-white text-xs bg-red-400/10 px-2 py-1 rounded">Удалить</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-6 rounded-2xl w-full max-w-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">{{ editId ? 'Редактировать триал' : 'Создать триал' }}</h2>
                    <button @click="closeModal" class="text-gray-400 hover:text-white">✕</button>
                </div>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Продукт</label>
                        <select v-model="form.product_id" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                            <option value="" disabled>Выберите продукт</option>
                            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Длительность (дней)</label>
                        <input v-model.number="form.duration_days" type="number" min="1" required class="input-field w-full px-4 py-2 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Макс. на пользователя</label>
                        <input v-model.number="form.max_per_user" type="number" min="1" required class="input-field w-full px-4 py-2 rounded-lg text-white">
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="active" type="checkbox" v-model="form.active" class="rounded">
                        <label for="active" class="text-sm text-gray-300">Активен</label>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeModal" class="text-gray-400 hover:text-white px-4">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">{{ editId ? 'Сохранить' : 'Создать' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            trials: [],
            products: [],
            search: '',
            showModal: false,
            editId: null,
            form: {
                product_id: '',
                duration_days: 7,
                max_per_user: 1,
                active: true
            }
        }
    },
    computed: {
        filteredTrials() {
            if (!this.search) return this.trials;
            const q = this.search.toLowerCase();
            return this.trials.filter(t => t.product?.name?.toLowerCase().includes(q));
        }
    },
    async mounted() {
        await Promise.all([this.fetchTrials(), this.fetchProducts()]);
    },
    methods: {
        async fetchTrials() {
            try {
                const res = await axios.get('/admin/trials');
                this.trials = res.data;
            } catch (e) {}
        },
        async fetchProducts() {
            try {
                const res = await axios.get('/products');
                this.products = res.data;
            } catch (e) {}
        },
        openCreate() {
            this.editId = null;
            this.form = { product_id: '', duration_days: 7, max_per_user: 1, active: true };
            this.showModal = true;
        },
        openEdit(t) {
            this.editId = t.id;
            this.form = { product_id: t.product_id, duration_days: t.duration_days, max_per_user: t.max_per_user, active: t.active };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        async submit() {
            try {
                if (this.editId) {
                    await axios.put(`/admin/trials/${this.editId}`, this.form);
                } else {
                    await axios.post('/admin/trials', this.form);
                }
                this.showModal = false;
                await this.fetchTrials();
            } catch (e) {
                alert('Ошибка сохранения');
            }
        },
        async remove(t) {
            if (!confirm('Удалить триал?')) return;
            try {
                await axios.delete(`/admin/trials/${t.id}`);
                this.fetchTrials();
            } catch (e) {
                alert('Удаление не удалось');
            }
        }
    }
}
</script>

<style scoped>
</style>

