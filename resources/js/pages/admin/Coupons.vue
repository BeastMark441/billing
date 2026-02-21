<template>
    <div class="space-y-8">
        <h1 class="text-3xl font-bold">Купоны</h1>
        
        <div class="glass-card p-6 rounded-2xl">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <router-link to="/admin" class="text-gray-400 hover:text-white text-sm">← Назад</router-link>
                    <input v-model="search" placeholder="Поиск по коду..." class="input-field w-full md:w-80 px-4 py-2 rounded-lg text-white">
                </div>
                <button @click="openCreate" class="btn-primary px-4 py-2 rounded-lg text-sm shadow-lg">+ Купон</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 text-sm border-b border-white/10">
                            <th class="p-4">ID</th>
                            <th class="p-4">Код</th>
                            <th class="p-4">Скидка (%)</th>
                            <th class="p-4">Макс. использований</th>
                            <th class="p-4">Истекает</th>
                            <th class="p-4">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in filteredCoupons" :key="c.id" class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500">#{{ c.id }}</td>
                            <td class="p-4 font-mono">{{ c.code }}</td>
                            <td class="p-4">{{ Number(c.discount).toFixed(2) }}</td>
                            <td class="p-4">{{ c.max_uses ?? '—' }}</td>
                            <td class="p-4 text-xs text-gray-400">{{ c.expires_at ? new Date(c.expires_at).toLocaleString() : '—' }}</td>
                            <td class="p-4 flex gap-2">
                                <button @click="openEdit(c)" class="text-blue-400 hover:text-white text-xs bg-blue-400/10 px-2 py-1 rounded">Редактировать</button>
                                <button @click="remove(c)" class="text-red-400 hover:text-white text-xs bg-red-400/10 px-2 py-1 rounded">Удалить</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-6 rounded-2xl w-full max-w-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">{{ editId ? 'Редактировать купон' : 'Создать купон' }}</h2>
                    <button @click="closeModal" class="text-gray-400 hover:text-white">✕</button>
                </div>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Код</label>
                        <input v-model="form.code" required class="input-field w-full px-4 py-2 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Скидка (%)</label>
                        <input v-model.number="form.discount" type="number" min="0" step="0.01" required class="input-field w-full px-4 py-2 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Макс. использований</label>
                        <input v-model.number="form.max_uses" type="number" min="1" class="input-field w-full px-4 py-2 rounded-lg text-white" placeholder="Необязательно">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Дата истечения</label>
                        <input v-model="form.expires_at" type="datetime-local" class="input-field w-full px-4 py-2 rounded-lg text-white">
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
            coupons: [],
            search: '',
            showModal: false,
            editId: null,
            form: {
                code: '',
                discount: 0,
                max_uses: null,
                expires_at: ''
            }
        }
    },
    computed: {
        filteredCoupons() {
            if (!this.search) return this.coupons;
            const q = this.search.toLowerCase();
            return this.coupons.filter(c => c.code.toLowerCase().includes(q));
        }
    },
    async mounted() {
        this.fetchCoupons();
    },
    methods: {
        async fetchCoupons() {
            try {
                const res = await axios.get('/admin/coupons');
                this.coupons = res.data;
            } catch (e) {}
        },
        openCreate() {
            this.editId = null;
            this.form = { code: '', discount: 0, max_uses: null, expires_at: '' };
            this.showModal = true;
        },
        openEdit(c) {
            this.editId = c.id;
            this.form = {
                code: c.code,
                discount: c.discount,
                max_uses: c.max_uses,
                expires_at: c.expires_at ? new Date(c.expires_at).toISOString().slice(0,16) : ''
            };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        async submit() {
            try {
                if (this.editId) {
                    await axios.put(`/admin/coupons/${this.editId}`, this.form);
                } else {
                    await axios.post('/admin/coupons', this.form);
                }
                this.showModal = false;
                await this.fetchCoupons();
            } catch (e) {
                alert('Ошибка сохранения');
            }
        },
        async remove(c) {
            if (!confirm('Удалить купон?')) return;
            try {
                await axios.delete(`/admin/coupons/${c.id}`);
                this.fetchCoupons();
            } catch (e) {
                alert('Удаление не удалось');
            }
        }
    }
}
</script>

<style scoped>
</style>

