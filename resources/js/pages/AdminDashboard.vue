<template>
    <div class="space-y-8">
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-white">Админ-панель</h1>
        
        <!-- Stats Overview (Mockup) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="glass-card p-6 rounded-2xl">
                <div class="text-gray-400 text-sm mb-1">Всего пользователей</div>
                <div class="text-2xl font-bold">{{ stats.users_count }}</div>
            </div>
            <div class="glass-card p-6 rounded-2xl">
                <div class="text-gray-400 text-sm mb-1">Активные серверы</div>
                <div class="text-2xl font-bold text-primary">{{ stats.active_servers }}</div>
            </div>
            <div class="glass-card p-6 rounded-2xl">
                <div class="text-gray-400 text-sm mb-1">Выручка за месяц</div>
                <div class="text-2xl font-bold text-green-400">{{ stats.monthly_revenue }}₽</div>
            </div>
             <div class="glass-card p-6 rounded-2xl">
                <div class="text-gray-400 text-sm mb-1">Загрузка нод</div>
                <div class="text-2xl font-bold text-blue-400">{{ stats.node_load }}%</div>
            </div>
        </div>

        <!-- Navigation Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <router-link to="/admin/users" class="glass-card p-8 rounded-2xl hover:bg-white/5 transition group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/20 transition">
                    <span class="sr-only">Пользователи</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" class="text-gray-300"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5a5 5 0 0 0 5 5m-7 8a7 7 0 1 1 14 0Z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Пользователи</h3>
                <p class="text-gray-400 text-sm">Управление клиентами, балансами и ролями.</p>
            </router-link>

            <router-link to="/admin/servers" class="glass-card p-8 rounded-2xl hover:bg-white/5 transition group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/20 transition">
                    <span class="sr-only">Серверы</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" class="text-gray-300"><path fill="currentColor" d="M4 6h16v4H4zm0 6h16v4H4z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Серверы</h3>
                <p class="text-gray-400 text-sm">Мониторинг серверов и управление питанием.</p>
            </router-link>

            <router-link to="/admin/tickets" class="glass-card p-8 rounded-2xl hover:bg-white/5 transition group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/20 transition">
                    <span class="sr-only">Тикеты</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" class="text-gray-300"><path fill="currentColor" d="M3 6h18v12H3z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Тикеты</h3>
                <p class="text-gray-400 text-sm">Обработка запросов поддержки.</p>
            </router-link>

            <router-link to="/admin/coupons" class="glass-card p-8 rounded-2xl hover:bg-white/5 transition group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/20 transition">
                    <span class="sr-only">Купоны</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" class="text-gray-300"><path fill="currentColor" d="M3 7h18v4h-3v2h3v4H3v-4h3v-2H3z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Купоны</h3>
                <p class="text-gray-400 text-sm">Управление промокодами и скидками.</p>
            </router-link>

            <router-link to="/admin/trials" class="glass-card p-8 rounded-2xl hover:bg-white/5 transition group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/20 transition">
                    <span class="sr-only">Триалы</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" class="text-gray-300"><path fill="currentColor" d="M5 5h14v14H5z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Триалы</h3>
                <p class="text-gray-400 text-sm">Пробные периоды по продуктам.</p>
            </router-link>

            <router-link to="/admin/categories" class="glass-card p-8 rounded-2xl hover:bg-white/5 transition group">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/20 transition">
                    <span class="sr-only">Категории</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" class="text-gray-300"><path fill="currentColor" d="M4 4h7v7H4zm9 0h7v7h-7zM4 13h7v7H4zm9 7v-7h7v7z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Категории</h3>
                <p class="text-gray-400 text-sm">Управление категориями тарифов.</p>
            </router-link>

             <!-- Existing Product/Node Management kept here for quick access or can be moved -->
             <div class="glass-card p-8 rounded-2xl">
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center text-2xl mb-4">⚙️</div>
                <h3 class="text-xl font-bold mb-2">Инфраструктура</h3>
                <p class="text-gray-400 text-sm mb-4">Управление тарифами и физическими нодами.</p>
                <div class="flex gap-2">
                     <!-- We can use local state to toggle modals if we keep the code in this file, 
                          but better to split. For now, let's keep the CRUD here as per previous step, 
                          but visually separate it. -->
                     <button @click="scrollToCrud" class="text-primary hover:underline text-sm">Перейти к настройкам ↓</button>
                </div>
            </div>
        </div>
        
        <div id="crud-section" class="pt-8 border-t border-white/5">
             <h2 class="text-2xl font-bold mb-6">Управление ресурсами</h2>
             <!-- Re-using existing CRUD components from previous step -->
             <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                 <!-- Nodes Section -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Узлы (Nodes)</h2>
                        <button @click="showNodeModal = true" class="btn-primary px-4 py-2 rounded-lg text-sm shadow-lg">
                            + Узел
                        </button>
                    </div>
                    <!-- Table content ... (Simplified for brevity, assuming user wants the full code) -->
                    <!-- I will paste the full table code back to ensure functionality is preserved -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-400 text-sm border-b border-white/10">
                                    <th class="p-3">Название</th>
                                    <th class="p-3">IP</th>
                                    <th class="p-3">Статус</th>
                                    <th class="p-3 text-right">Действия</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr v-for="node in nodes" :key="node.id" class="border-b border-white/5">
                                    <td class="p-3 font-bold">{{ node.name }}</td>
                                    <td class="p-3 font-mono text-gray-400">{{ node.ip }}</td>
                                    <td class="p-3">
                                        <span :class="node.is_active ? 'text-green-400' : 'text-red-400'" class="font-bold text-xs uppercase">{{ node.is_active ? 'ON' : 'OFF' }}</span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <button @click="openEditNode(node)" class="text-gray-400 hover:text-white">✎</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="glass-card p-6 rounded-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold">Тарифы</h2>
                        <button @click="showProductModal = true" class="btn-primary px-4 py-2 rounded-lg text-sm shadow-lg">
                            + Тариф
                        </button>
                    </div>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        <div v-for="product in products" :key="product.id" class="bg-black/20 p-4 rounded-xl border border-white/5 flex justify-between items-center">
                            <div>
                                <div class="font-bold">{{ product.name }}</div>
                                <div class="text-xs text-gray-400">{{ product.resources.cpu }}% CPU / {{ product.resources.ram }} MB RAM</div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-bold">{{ product.price_monthly }}₽</span>
                                <button @click="openEditProduct(product)" class="text-gray-400 hover:text-white">✎</button>
                                <button @click="deleteProduct(product.id)" class="text-red-400 hover:text-red-300">×</button>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        </div>

        <!-- Modals (kept from previous step) -->
        <div v-if="showNodeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-8 rounded-2xl w-full max-w-lg">
                <h3 class="text-2xl font-bold mb-6">Добавить узел</h3>
                <form @submit.prevent="createNode" class="space-y-4">
                    <input v-model="newNode.name" placeholder="Название" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <input v-model="newNode.ptero_id" placeholder="ID" type="number" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <input v-model="newNode.ip" placeholder="IP" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <div class="flex gap-4">
                        <input v-model="newNode.port_range_start" placeholder="Start" type="number" class="input-field w-1/2 px-4 py-3 rounded-xl text-white" required>
                        <input v-model="newNode.port_range_end" placeholder="End" type="number" class="input-field w-1/2 px-4 py-3 rounded-xl text-white" required>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showNodeModal = false" class="text-gray-400">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditNodeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-8 rounded-2xl w-full max-w-lg">
                <h3 class="text-2xl font-bold mb-6">Редактировать узел</h3>
                <form @submit.prevent="updateNode" class="space-y-4">
                    <input v-model="editingNode.name" placeholder="Название" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <input v-model="editingNode.ptero_id" placeholder="ID" type="number" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <input v-model="editingNode.ip" placeholder="IP" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <div class="flex gap-4">
                        <input v-model="editingNode.port_range_start" placeholder="Start" type="number" class="input-field w-1/2 px-4 py-3 rounded-xl text-white" required>
                        <input v-model="editingNode.port_range_end" placeholder="End" type="number" class="input-field w-1/2 px-4 py-3 rounded-xl text-white" required>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" v-model="editingNode.is_active">
                        <span class="text-sm text-gray-300">Узел активен</span>
                    </label>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showEditNodeModal = false" class="text-gray-400">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Сохранить</button>
                    </div>
                    <div v-if="nodeFormError" class="text-red-400 text-sm mt-2">{{ nodeFormError }}</div>
                </form>
            </div>
        </div>

        <div v-if="showProductModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-8 rounded-2xl w-full max-w-lg">
                <h3 class="text-2xl font-bold mb-6">Добавить тариф</h3>
                <form @submit.prevent="createProduct" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Название тарифа</label>
                            <input v-model="newProduct.name" placeholder="Напр.: Стартовый Minecraft" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                            <p class="text-xs text-gray-500 mt-1">Короткое и понятное название.</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Цена, ₽/месяц</label>
                            <input v-model.number="newProduct.price_monthly" placeholder="Напр.: 299" type="number" min="0" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                            <p class="text-xs text-gray-500 mt-1">Укажите цену без пробелов и валюты.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Тип тарифа</label>
                            <select v-model="newProduct.type" class="input-field w-full px-4 py-3 rounded-xl text-white">
                                <option value="game">Игровой</option>
                                <option value="vps">VPS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Категория</label>
                            <select v-model="newProduct.category_id" required class="input-field w-full px-4 py-3 rounded-xl text-white">
                                <option value="" disabled>Выберите категорию</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id" :disabled="!c.is_visible">
                                    {{ c.name }} ({{ c.slug }}) {{ c.is_visible ? '' : '— скрыта' }}
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Обязательное поле. Только существующие категории.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Описание тарифа</label>
                        <textarea v-model="newProduct.description" placeholder="Кратко опишите сценарии использования, особенности и ограничения..." class="input-field w-full px-4 py-3 rounded-xl text-white h-24"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Отображается на карточке тарифа для клиента.</p>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">CPU, %</label>
                            <input v-model.number="newProduct.resources.cpu" placeholder="Напр.: 100" type="number" min="10" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">RAM, MB</label>
                            <input v-model.number="newProduct.resources.ram" placeholder="Напр.: 2048" type="number" min="256" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Disk, MB</label>
                            <input v-model.number="newProduct.resources.disk" placeholder="Напр.: 10240" type="number" min="1024" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Порты, шт.</label>
                            <input v-model.number="newProduct.resources.ports" placeholder="Напр.: 1" type="number" min="1" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Nest ID</label>
                            <input v-model.number="newProduct.resources.nest_id" placeholder="Напр.: 1" type="number" min="1" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Egg ID</label>
                            <input v-model.number="newProduct.resources.egg_id" placeholder="Напр.: 1" type="number" min="1" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="newProduct.is_active">
                            <span class="text-sm text-gray-300">Тариф активен</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="newProduct.is_hidden">
                            <span class="text-sm text-gray-300">Скрыть от клиентов</span>
                        </label>
                    </div>

                    <div class="border-t border-white/10 pt-4">
                        <label class="block text-sm font-bold mb-2">Привязка к нодам (опционально)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label v-for="node in nodes" :key="node.id" class="flex items-center gap-2 p-2 bg-white/5 rounded hover:bg-white/10 cursor-pointer">
                                <input type="checkbox" :value="node.id" v-model="newProduct.nodes" class="rounded border-white/20 bg-black/20">
                                <span class="text-sm">{{ node.name }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showProductModal = false" class="text-gray-400">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditProductModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-8 rounded-2xl w-full max-w-lg">
                <h3 class="text-2xl font-bold mb-6">Редактировать тариф</h3>
                <form @submit.prevent="updateProduct" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Название тарифа</label>
                            <input v-model="editingProduct.name" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Цена, ₽/месяц</label>
                            <input v-model.number="editingProduct.price_monthly" type="number" min="0" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Тип тарифа</label>
                            <select v-model="editingProduct.type" class="input-field w-full px-4 py-3 rounded-xl text-white">
                                <option value="game">Игровой</option>
                                <option value="vps">VPS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Категория</label>
                            <select v-model="editingProduct.category_id" class="input-field w-full px-4 py-3 rounded-xl text-white">
                                <option value="" disabled>Выберите категорию</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">
                                    {{ c.name }} ({{ c.slug }})
                                </option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Описание</label>
                        <textarea v-model="editingProduct.description" class="input-field w-full px-4 py-3 rounded-xl text-white h-24"></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">CPU, %</label>
                            <input v-model.number="editingProduct.resources.cpu" type="number" min="10" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">RAM, MB</label>
                            <input v-model.number="editingProduct.resources.ram" type="number" min="256" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Disk, MB</label>
                            <input v-model.number="editingProduct.resources.disk" type="number" min="1024" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Порты, шт.</label>
                            <input v-model.number="editingProduct.resources.ports" type="number" min="1" class="input-field w-full px-4 py-3 rounded-xl text-white">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Nest ID</label>
                            <input v-model.number="editingProduct.resources.nest_id" type="number" min="1" class="input-field w-full px-4 py-3 rounded-xl text-white">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Egg ID</label>
                            <input v-model.number="editingProduct.resources.egg_id" type="number" min="1" class="input-field w-full px-4 py-3 rounded-xl text-white">
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-4">
                        <label class="block text-sm font-bold mb-2">Привязка к нодам</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label v-for="node in nodes" :key="node.id" class="flex items-center gap-2 p-2 bg-white/5 rounded hover:bg-white/10 cursor-pointer">
                                <input type="checkbox" :value="node.id" v-model="editingProduct.nodes" class="rounded border-white/20 bg-black/20">
                                <span class="text-sm">{{ node.name }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="editingProduct.is_active">
                            <span class="text-sm text-gray-300">Тариф активен</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="editingProduct.is_hidden">
                            <span class="text-sm text-gray-300">Скрыть от клиентов</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showEditProductModal = false" class="text-gray-400">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Сохранить</button>
                    </div>
                    <div v-if="productFormError" class="text-red-400 text-sm mt-2">{{ productFormError }}</div>
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
            stats: {
                users_count: 0,
                active_servers: 0,
                monthly_revenue: 0,
                node_load: 0
            },
            nodes: [],
            categories: [],
            products: [],
            showNodeModal: false,
            showProductModal: false,
            newNode: {
                name: '',
                ptero_id: '',
                ip: '',
                port_range_start: 25565,
                port_range_end: 25600,
                is_active: true
            },
            newProduct: {
                name: '',
                description: '',
                type: 'game',
                category_id: '',
                price_monthly: '',
                resources: {
                    cpu: 100,
                    ram: 1024,
                    disk: 10240,
                    ports: 1,
                    nest_id: 1,
                    egg_id: 1
                },
                is_active: true,
                is_hidden: false,
                nodes: []
            },
            showEditProductModal: false,
            editingProduct: null,
            productFormError: '',
            showEditNodeModal: false,
            editingNode: null,
            nodeFormError: ''
        }
    },
    async mounted() {
        this.fetchData();
        this.fetchStats();
    },
    methods: {
        async fetchStats() {
            try {
                const res = await axios.get('/admin/stats');
                this.stats = res.data;
            } catch (error) {
                console.error(error);
            }
        },
        async fetchData() {
            try {
                const [nodesRes, productsRes, categoriesRes] = await Promise.all([
                    axios.get('/admin/nodes'),
                    axios.get('/products'),
                    axios.get('/admin/categories')
                ]);
                this.nodes = nodesRes.data;
                this.products = productsRes.data;
                this.categories = categoriesRes.data;
            } catch (error) {
                console.error(error);
            }
        },
        async createNode() {
            try {
                await axios.post('/admin/nodes', this.newNode);
                this.showNodeModal = false;
                this.fetchData();
                this.newNode = { name: '', ptero_id: '', ip: '', port_range_start: 25565, port_range_end: 25600, is_active: true };
            } catch (error) {
                this.nodeFormError = error.response?.data?.message || 'Не удалось создать узел';
            }
        },
        async createProduct() {
            try {
                if (!this.newProduct.category_id) {
                    this.productFormError = 'Пожалуйста, выберите категорию.';
                    return;
                }
                await axios.post('/admin/products', this.newProduct);
                this.showProductModal = false;
                this.fetchData();
                this.newProduct = {
                    name: '',
                    description: '',
                    type: 'game',
                    category_id: '',
                    price_monthly: '',
                    resources: { cpu: 100, ram: 1024, disk: 10240, ports: 1, nest_id: 1, egg_id: 1 },
                    is_active: true,
                    is_hidden: false,
                    nodes: []
                };
                this.productFormError = '';
            } catch (error) {
                this.productFormError = error.response?.data?.message || 'Не удалось создать тариф';
            }
        },
        async deleteProduct(id) {
            if (!confirm('Delete?')) return;
            try {
                await axios.delete(`/admin/products/${id}`);
                this.fetchData();
            } catch (error) {
                this.productFormError = error.response?.data?.message || 'Не удалось удалить тариф';
            }
        },
        openEditProduct(product) {
            this.editingProduct = JSON.parse(JSON.stringify(product));
            if (!this.editingProduct.resources) {
                this.editingProduct.resources = { cpu: 100, ram: 1024, disk: 10240, ports: 1, nest_id: 1, egg_id: 1 };
            }
            // normalize nodes to array of IDs for checkbox binding
            if (Array.isArray(this.editingProduct.nodes)) {
                this.editingProduct.nodes = this.editingProduct.nodes.map(n => n.id ?? n).filter(Boolean);
            } else {
                this.editingProduct.nodes = [];
            }
            // ensure category_id present for select
            if (this.editingProduct.category && this.editingProduct.category.id && !this.editingProduct.category_id) {
                this.editingProduct.category_id = this.editingProduct.category.id;
            }
            this.productFormError = '';
            this.showEditProductModal = true;
        },
        async updateProduct() {
            if (!this.editingProduct) return;
            try {
                const payload = JSON.parse(JSON.stringify(this.editingProduct));
                // remove relation objects to avoid backend validation issues
                delete payload.category;
                // nodes must be array of ids
                if (!Array.isArray(payload.nodes)) payload.nodes = [];
                await axios.put(`/admin/products/${payload.id}`, payload);
                this.showEditProductModal = false;
                this.fetchData();
            } catch (error) {
                this.productFormError = error.response?.data?.message || 'Не удалось сохранить тариф';
            }
        },
        openEditNode(node) {
            this.editingNode = JSON.parse(JSON.stringify(node));
            this.nodeFormError = '';
            this.showEditNodeModal = true;
        },
        async updateNode() {
            if (!this.editingNode) return;
            try {
                await axios.put(`/admin/nodes/${this.editingNode.id}`, this.editingNode);
                this.showEditNodeModal = false;
                this.fetchData();
            } catch (error) {
                this.nodeFormError = error.response?.data?.message || 'Не удалось сохранить узел';
            }
        },
        scrollToCrud() {
            document.getElementById('crud-section').scrollIntoView({ behavior: 'smooth' });
        }
    }
}
</script>
