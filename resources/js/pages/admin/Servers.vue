<template>
    <div class="space-y-8">
        <h1 class="text-3xl font-bold">Серверы</h1>
        
        <div class="glass-card p-6 rounded-2xl">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <router-link to="/admin" class="text-gray-400 hover:text-white text-sm">← Назад</router-link>
                    <input v-model="search" placeholder="Поиск по названию, IP или пользователю..." class="input-field w-full md:w-80 px-4 py-2 rounded-lg text-white">
                </div>
                <button @click="showCreate = true" class="btn-primary px-4 py-2 rounded-lg text-sm shadow-lg">+ Выдать сервер</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 text-sm border-b border-white/10">
                            <th class="p-4">ID</th>
                            <th class="p-4">Название</th>
                            <th class="p-4">Пользователь</th>
                            <th class="p-4">Тариф</th>
                            <th class="p-4">IP:Port</th>
                            <th class="p-4">Истекает</th>
                            <th class="p-4">Статус</th>
                            <th class="p-4">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="server in filteredServers" :key="server.id" class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500">#{{ server.id }}</td>
                            <td class="p-4 font-bold">{{ server.name }}</td>
                            <td class="p-4 text-primary">{{ server.user?.email }}</td>
                            <td class="p-4">{{ server.product?.name }}</td>
                            <td class="p-4 font-mono text-gray-400">{{ server.ip }}:{{ server.port }}</td>
                            <td class="p-4 text-gray-400 text-xs">{{ new Date(server.expires_at).toLocaleDateString() }}</td>
                            <td class="p-4">
                                <span :class="{'text-green-400 bg-green-400/10': server.status === 'active', 'text-red-400 bg-red-400/10': server.status !== 'active'}" class="px-2 py-1 rounded text-xs uppercase font-bold">
                                    {{ server.status }}
                                </span>
                            </td>
                            <td class="p-4 flex gap-2 flex-wrap">
                                <button v-if="server.status === 'active'" @click="changeStatus(server, 'suspended')" class="text-yellow-400 hover:text-white text-xs bg-yellow-400/10 px-2 py-1 rounded">Suspend</button>
                                <button v-else @click="changeStatus(server, 'active')" class="text-green-400 hover:text-white text-xs bg-green-400/10 px-2 py-1 rounded">Unsuspend</button>
                                
                                <button @click="openChangePlan(server)" class="text-blue-400 hover:text-white text-xs bg-blue-400/10 px-2 py-1 rounded">Изм. тариф</button>
                                <button @click="cancelWithRefund(server)" class="text-red-400 hover:text-white text-xs bg-red-400/10 px-2 py-1 rounded">Отмена+Возврат</button>
                                <button @click="deleteServer(server)" class="text-red-400 hover:text-white text-xs bg-red-400/10 px-2 py-1 rounded">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Change Plan Modal -->
        <div v-if="showChangePlan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-6 rounded-2xl w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">Сменить тариф</h2>
                    <button @click="closeChangePlan" class="text-gray-400 hover:text-white">✕</button>
                </div>
                <form @submit.prevent="confirmChangePlan" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Новый тариф</label>
                        <select v-model="changePlanForm.product_id" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                            <option value="" disabled>Выберите тариф</option>
                            <option v-for="p in products" :key="p.id" :value="p.id">
                                {{ p.name }} ({{ p.category?.name || 'Без категории' }}) — {{ p.price_monthly }} ₽
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeChangePlan" class="text-gray-400 px-4">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Сменить</button>
                    </div>
                    <div v-if="changePlanError" class="text-red-400 text-sm">{{ changePlanError }}</div>
                </form>
            </div>
        </div>
        <!-- Create Server Modal -->
        <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-6 rounded-2xl w-full max-w-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">Выдать сервер</h2>
                    <button @click="showCreate = false" class="text-gray-400 hover:text-white">✕</button>
                </div>
                <form @submit.prevent="createServer" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Пользователь</label>
                        <select v-model="form.user_id" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                            <option value="" disabled>Выберите пользователя</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">
                                #{{ u.id }} — {{ u.email }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Тариф</label>
                        <select v-model="form.product_id" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                            <option value="" disabled>Выберите тариф</option>
                            <option v-for="p in products" :key="p.id" :value="p.id">
                                {{ p.name }} ({{ p.category?.name || 'Без категории' }})
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showCreate = false" class="text-gray-400 hover:text-white px-4">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Создать</button>
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
            servers: [],
            search: '',
            showCreate: false,
            users: [],
            products: [],
            form: {
                user_id: '',
                product_id: ''
            },
            showChangePlan: false,
            changePlanForm: {
                server_id: null,
                product_id: ''
            },
            changePlanError: ''
        }
    },
    computed: {
        filteredServers() {
            if (!this.search) return this.servers;
            const q = this.search.toLowerCase();
            return this.servers.filter(s => 
                s.name.toLowerCase().includes(q) || 
                s.ip.includes(q) ||
                s.user?.email.toLowerCase().includes(q)
            );
        }
    },
    async mounted() {
        await this.fetchServers();
        await this.fetchUsersAndProducts();
    },
    methods: {
        async fetchServers() {
            try {
                const res = await axios.get('/admin/servers');
                this.servers = res.data;
            } catch (error) {
                console.error(error);
            }
        },
        async fetchUsersAndProducts() {
            try {
                const [usersRes, productsRes] = await Promise.all([
                    axios.get('/admin/users'),
                    axios.get('/products')
                ]);
                this.users = usersRes.data;
                this.products = productsRes.data;
            } catch (e) {}
        },
        async changeStatus(server, status) {
            if (!confirm(`Change status to ${status}?`)) return;
            try {
                await axios.put(`/admin/servers/${server.id}`, { status });
                this.fetchServers();
            } catch (error) {
                alert('Update failed');
            }
        },
        async deleteServer(server) {
            if (!confirm('Delete server? This will destroy it on Pterodactyl too.')) return;
            try {
                await axios.delete(`/admin/servers/${server.id}`);
                this.fetchServers();
            } catch (error) {
                alert('Delete failed');
            }
        },
        async createServer() {
            try {
                await axios.post('/admin/servers', this.form);
                this.showCreate = false;
                this.form = { user_id: '', product_id: '' };
                await this.fetchServers();
                alert('Сервер выдан');
            } catch (error) {
                alert(error.response?.data?.error || 'Не удалось создать сервер');
            }
        },
        openChangePlan(server) {
            this.changePlanForm.server_id = server.id;
            this.changePlanForm.product_id = '';
            this.changePlanError = '';
            this.showChangePlan = true;
        },
        closeChangePlan() {
            this.showChangePlan = false;
        },
        async confirmChangePlan() {
            if (!this.changePlanForm.server_id || !this.changePlanForm.product_id) return;
            try {
                await axios.post(`/admin/servers/${this.changePlanForm.server_id}/change-plan`, {
                    product_id: this.changePlanForm.product_id
                });
                this.showChangePlan = false;
                await this.fetchServers();
                alert('Тариф изменён');
            } catch (error) {
                this.changePlanError = error.response?.data?.error || 'Не удалось сменить тариф';
            }
        },
        async cancelWithRefund(server) {
            if (!confirm('Отменить сервер и выполнить пропорциональный возврат пользователю?')) return;
            try {
                const res = await axios.post(`/admin/servers/${server.id}/cancel`);
                await this.fetchServers();
                alert(`Сервер отменён. Возврат: ${res.data?.refund ?? 0} ₽`);
            } catch (error) {
                alert(error.response?.data?.error || 'Не удалось отменить сервер');
            }
        }
    }
}
</script>
