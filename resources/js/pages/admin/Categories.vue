<template>
    <div class="space-y-8">
        <h1 class="text-3xl font-bold">Категории</h1>
        
        <div class="glass-card p-6 rounded-2xl">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <router-link to="/admin" class="text-gray-400 hover:text-white text-sm">← Назад</router-link>
                    <input v-model="search" placeholder="Поиск по названию или slug..." class="input-field w-full md:w-96 px-4 py-2 rounded-lg text-white">
                </div>
                <button @click="openCreate" class="btn-primary px-4 py-2 rounded-lg text-sm shadow-lg">+ Категория</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 text-sm border-b border-white/10">
                            <th class="p-4">ID</th>
                            <th class="p-4">Название</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4">Описание</th>
                            <th class="p-4">Картинка</th>
                            <th class="p-4">Видимость</th>
                            <th class="p-4">Тарифов</th>
                            <th class="p-4">Массовые действия</th>
                            <th class="p-4">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cat in filtered" :key="cat.id" class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500">#{{ cat.id }}</td>
                            <td class="p-4 font-bold">{{ cat.name }}</td>
                            <td class="p-4 font-mono text-gray-400">{{ cat.slug }}</td>
                            <td class="p-4 text-gray-300">{{ cat.description || '—' }}</td>
                            <td class="p-4">
                                <img v-if="cat.image_url" :src="cat.image_url" class="w-14 h-14 object-cover rounded border border-white/10" alt="">
                                <span v-else class="text-gray-500 text-sm">—</span>
                            </td>
                            <td class="p-4">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" v-model="cat.is_visible" @change="toggleVisibility(cat)" />
                                    <span :class="cat.is_visible ? 'text-green-400' : 'text-red-400'">{{ cat.is_visible ? 'Виден' : 'Скрыт' }}</span>
                                </label>
                            </td>
                            <td class="p-4 text-gray-300">{{ cat.products_count }}</td>
                            <td class="p-4 flex gap-2">
                                <button @click="bulkProducts(cat, false)" class="text-red-400 hover:text-white text-xs bg-red-400/10 px-2 py-1 rounded">Скрыть все тарифы</button>
                                <button @click="bulkProducts(cat, true)" class="text-green-400 hover:text-white text-xs bg-green-400/10 px-2 py-1 rounded">Показать все тарифы</button>
                            </td>
                            <td class="p-4 flex gap-2">
                                <button @click="openEdit(cat)" class="text-blue-400 hover:text-white text-xs bg-blue-400/10 px-2 py-1 rounded">Редактировать</button>
                                <button @click="remove(cat)" class="text-red-400 hover:text-white text-xs bg-red-400/10 px-2 py-1 rounded">Удалить</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-6 rounded-2xl w-full max-w-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">{{ editId ? 'Редактировать категорию' : 'Новая категория' }}</h2>
                    <button @click="closeModal" class="text-gray-400 hover:text-white">✕</button>
                </div>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Название</label>
                        <input v-model="form.name" required class="input-field w-full px-4 py-2 rounded-lg text-white" placeholder="Напр.: Minecraft">
                        <p class="text-xs text-gray-500 mt-1">Короткое человеческое название категории.</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Slug</label>
                        <input v-model="form.slug" required class="input-field w-full px-4 py-2 rounded-lg text-white" placeholder="Напр.: minecraft">
                        <p class="text-xs text-gray-500 mt-1">Латиницей, используется в адресах и фильтрах.</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Описание</label>
                        <textarea v-model="form.description" class="input-field w-full px-4 py-2 rounded-lg text-white h-24" placeholder="Кратко опишите назначение и состав тарифов..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Отображается в админке и может использоваться в каталоге.</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Изображение (URL)</label>
                        <input v-model="form.image_url" class="input-field w-full px-4 py-2 rounded-lg text-white" placeholder="https://...">
                        <p class="text-xs text-gray-500 mt-1">Опционально. Ссылка на картинку категории (показывается на странице услуг).</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="is_visible" type="checkbox" v-model="form.is_visible">
                        <label for="is_visible" class="text-sm text-gray-300">Категория видна клиентам</label>
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
            categories: [],
            search: '',
            showModal: false,
            editId: null,
            form: {
                name: '',
                slug: '',
                description: '',
                image_url: '',
                is_visible: true
            }
        }
    },
    computed: {
        filtered() {
            if (!this.search) return this.categories;
            const q = this.search.toLowerCase();
            return this.categories.filter(c => c.name.toLowerCase().includes(q) || c.slug.toLowerCase().includes(q));
        }
    },
    async mounted() {
        this.fetch();
    },
    methods: {
        async fetch() {
            const res = await axios.get('/admin/categories');
            this.categories = res.data;
        },
        openCreate() {
            this.editId = null;
            this.form = { name: '', slug: '', description: '', image_url: '', is_visible: true };
            this.showModal = true;
        },
        openEdit(cat) {
            this.editId = cat.id;
            this.form = { name: cat.name, slug: cat.slug, description: cat.description, image_url: cat.image_url, is_visible: cat.is_visible };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        async submit() {
            if (this.editId) {
                await axios.put(`/admin/categories/${this.editId}`, this.form);
            } else {
                await axios.post('/admin/categories', this.form);
            }
            this.showModal = false;
            this.fetch();
        },
        async toggleVisibility(cat) {
            await axios.post(`/admin/categories/${cat.id}/visibility`, { is_visible: !!cat.is_visible });
        },
        async bulkProducts(cat, show) {
            await axios.post(`/admin/categories/${cat.id}/products/visibility`, { is_hidden: !show });
            alert(show ? 'Все тарифы показаны' : 'Все тарифы скрыты');
        },
        async remove(cat) {
            if (!confirm('Удалить категорию?')) return;
            await axios.delete(`/admin/categories/${cat.id}`);
            this.fetch();
        }
    }
}
</script>

<style scoped>
</style>
