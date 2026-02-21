<template>
    <div class="space-y-8">
        <h1 class="text-3xl font-bold">Пользователи</h1>
        
        <div class="glass-card p-6 rounded-2xl">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <router-link to="/admin" class="text-gray-400 hover:text-white text-sm">← Назад</router-link>
                    <input v-model="search" placeholder="Поиск по email или имени..." class="input-field w-full md:w-96 px-4 py-2 rounded-lg text-white">
                </div>
                <button @click="showCreate = true" class="btn-primary px-4 py-2 rounded-lg text-sm shadow-lg">+ Пользователь</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 text-sm border-b border-white/10">
                            <th class="p-4">ID</th>
                            <th class="p-4">Имя</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Баланс</th>
                            <th class="p-4">Роль</th>
                            <th class="p-4">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in filteredUsers" :key="user.id" class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500">#{{ user.id }}</td>
                            <td class="p-4 font-bold">{{ user.name }}</td>
                            <td class="p-4">{{ user.email }}</td>
                            <td class="p-4 font-mono text-primary">{{ user.balance }}₽</td>
                            <td class="p-4">
                                <span :class="user.role === 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-gray-700/50 text-gray-400'" class="px-2 py-1 rounded text-xs uppercase font-bold">
                                    {{ user.role }}
                                </span>
                                <span v-if="user.is_blocked" class="ml-2 bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs uppercase font-bold">Blocked</span>
                            </td>
                            <td class="p-4">
                                <router-link :to="'/admin/users/' + user.id" class="text-primary hover:text-white text-sm">Управление</router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create User Modal -->
        <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-6 rounded-2xl w-full max-w-lg">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">Создать пользователя</h2>
                    <button @click="showCreate = false" class="text-gray-400 hover:text-white">✕</button>
                </div>
                <form @submit.prevent="createUser" class="space-y-4">
                    <input v-model="createForm.name" placeholder="Никнейм" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                    <input v-model="createForm.email" type="email" placeholder="Email" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                    <input v-model="createForm.password" type="password" placeholder="Пароль" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                    <input v-model="createForm.password_confirmation" type="password" placeholder="Подтверждение" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
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
            users: [],
            search: '',
            editingUser: null,
            showCreate: false,
            createForm: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    computed: {
        filteredUsers() {
            if (!this.search) return this.users;
            const q = this.search.toLowerCase();
            return this.users.filter(u => 
                u.name.toLowerCase().includes(q) || 
                u.email.toLowerCase().includes(q)
            );
        }
    },
    async mounted() {
        this.fetchUsers();
    },
    methods: {
        async fetchUsers() {
            try {
                const res = await axios.get('/admin/users');
                this.users = res.data;
            } catch (error) {
                console.error(error);
            }
        },
        async createUser() {
            try {
                await axios.post('/admin/users', this.createForm);
                this.showCreate = false;
                this.createForm = { name:'', email:'', password:'', password_confirmation:'' };
                this.fetchUsers();
            } catch (error) {
                alert(error.response?.data?.message || 'Ошибка создания');
            }
        }
    }
}
</script>
