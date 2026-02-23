<template>
    <div v-if="user" class="space-y-8">
        <div class="flex items-center gap-4">
            <router-link to="/backoffice/users" class="text-gray-400 hover:text-white">← Назад</router-link>
            <h1 class="text-3xl font-bold">Пользователь: {{ user.name }}</h1>
            <span v-if="user.is_blocked" class="bg-red-500/20 text-red-400 px-3 py-1 rounded-lg text-sm font-bold uppercase">Заблокирован</span>
        </div>

        <div class="flex gap-4 border-b border-white/10 overflow-x-auto pb-1">
            <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-primary border-primary' : 'text-gray-400 border-transparent hover:text-white'" class="px-4 py-2 border-b-2 font-bold transition">Обзор</button>
            <button @click="activeTab = 'servers'" :class="activeTab === 'servers' ? 'text-primary border-primary' : 'text-gray-400 border-transparent hover:text-white'" class="px-4 py-2 border-b-2 font-bold transition">Серверы</button>
            <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'text-primary border-primary' : 'text-gray-400 border-transparent hover:text-white'" class="px-4 py-2 border-b-2 font-bold transition">Платежи</button>
            <button @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'text-primary border-primary' : 'text-gray-400 border-transparent hover:text-white'" class="px-4 py-2 border-b-2 font-bold transition">Логи</button>
        </div>

        <!-- Overview Tab -->
        <div v-if="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 glass-card p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <router-link to="/backoffice/users" class="text-gray-400 hover:text-white text-sm">← Назад</router-link>
                    <button @click="deleteUser" class="text-red-400 hover:text-white bg-red-400/10 px-3 py-1.5 rounded text-sm font-bold">Удалить пользователя</button>
                </div>
                <h2 class="text-xl font-bold mb-6">Редактирование профиля</h2>
                <form @submit.prevent="updateUser" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-400 text-sm block mb-1">Email</label>
                            <input v-model="user.email" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                         <div>
                            <label class="text-gray-400 text-sm block mb-1">Никнейм</label>
                            <input v-model="user.name" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-gray-400 text-sm block mb-1">Фамилия</label>
                            <input v-model="user.last_name" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm block mb-1">Имя</label>
                            <input v-model="user.first_name" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                         <div>
                            <label class="text-gray-400 text-sm block mb-1">Отчество</label>
                            <input v-model="user.middle_name" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-gray-400 text-sm block mb-1">Телефон</label>
                            <input v-model="user.phone" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                         <div>
                            <label class="text-gray-400 text-sm block mb-1">Telegram</label>
                            <input v-model="user.telegram" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                         <div>
                            <label class="text-gray-400 text-sm block mb-1">VK</label>
                            <input v-model="user.vk" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-white/10 pt-4">
                         <div>
                            <label class="text-gray-400 text-sm block mb-1">Баланс</label>
                            <input v-model="user.balance" type="number" class="input-field w-full px-4 py-2 rounded-lg text-white">
                        </div>
                         <div>
                            <label class="text-gray-400 text-sm block mb-1">Роль</label>
                            <select v-model="user.role" class="input-field w-full px-4 py-2 rounded-lg text-white">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Сохранить изменения</button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-xl font-bold mb-4">Действия</h2>
                    <div class="space-y-3">
                        <button @click="toggleBlock" :class="user.is_blocked ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' : 'bg-red-500/20 text-red-400 hover:bg-red-500/30'" class="w-full py-2 rounded-lg font-bold transition">
                            {{ user.is_blocked ? 'Разблокировать' : 'Заблокировать' }}
                        </button>
                        <button @click="verifyEmail" class="w-full bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 py-2 rounded-lg font-bold transition">
                            Подтвердить почту
                        </button>
                        <button @click="showResetPassword = true" class="w-full bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30 py-2 rounded-lg font-bold transition">
                            Сбросить пароль
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servers Tab -->
        <div v-if="activeTab === 'servers'" class="glass-card p-6 rounded-2xl">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-sm border-b border-white/10">
                        <th class="p-4">ID</th>
                        <th class="p-4">Название</th>
                        <th class="p-4">IP</th>
                        <th class="p-4">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="server in user.servers" :key="server.id" class="border-b border-white/5">
                        <td class="p-4 text-gray-500">#{{ server.id }}</td>
                        <td class="p-4 font-bold">{{ server.name }}</td>
                        <td class="p-4 text-gray-400">{{ server.ip }}:{{ server.port }}</td>
                        <td class="p-4">
                             <span :class="{'text-green-400': server.status === 'active', 'text-red-400': server.status !== 'active'}" class="text-xs uppercase font-bold">
                                {{ server.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payments Tab -->
        <div v-if="activeTab === 'payments'" class="glass-card p-6 rounded-2xl">
             <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-sm border-b border-white/10">
                        <th class="p-4">ID</th>
                        <th class="p-4">Сумма</th>
                        <th class="p-4">Статус</th>
                        <th class="p-4">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="payment in user.payments" :key="payment.id" class="border-b border-white/5">
                        <td class="p-4 text-gray-500">#{{ payment.id }}</td>
                        <td class="p-4 font-bold font-mono">{{ payment.amount }}₽</td>
                        <td class="p-4 uppercase text-xs font-bold">{{ payment.status }}</td>
                        <td class="p-4 text-gray-400 text-sm">{{ new Date(payment.created_at).toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

         <!-- Logs Tab -->
        <div v-if="activeTab === 'logs'" class="glass-card p-6 rounded-2xl">
             <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-sm border-b border-white/10">
                        <th class="p-4">IP</th>
                        <th class="p-4">User Agent</th>
                        <th class="p-4">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in user.login_logs" :key="log.id" class="border-b border-white/5">
                        <td class="p-4 font-mono text-gray-300">{{ log.ip }}</td>
                        <td class="p-4 text-xs text-gray-500 max-w-xs truncate" :title="log.user_agent">{{ log.user_agent }}</td>
                        <td class="p-4 text-gray-400 text-sm">{{ new Date(log.created_at).toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Reset Password Modal -->
        <div v-if="showResetPassword" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-8 rounded-2xl w-full max-w-md">
                <h3 class="text-2xl font-bold mb-6">Сброс пароля</h3>
                <form @submit.prevent="resetPassword" class="space-y-4">
                    <input v-model="newPassword" type="text" placeholder="Новый пароль" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                    <input v-model="newPasswordConfirm" type="text" placeholder="Подтверждение" class="input-field w-full px-4 py-2 rounded-lg text-white" required>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showResetPassword = false" class="text-gray-400 hover:text-white px-4">Отмена</button>
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg">Сбросить</button>
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
            user: null,
            activeTab: 'overview',
            showResetPassword: false,
            newPassword: '',
            newPasswordConfirm: ''
        }
    },
    async mounted() {
        this.fetchUser();
    },
    methods: {
        async fetchUser() {
            try {
                const res = await axios.get(`/admin/users/${this.$route.params.id}`);
                this.user = res.data;
            } catch (error) {
                console.error(error);
                this.$router.push('/backoffice/users');
            }
        },
        async updateUser() {
            try {
                await axios.put(`/admin/users/${this.user.id}`, this.user);
                alert('Данные сохранены');
            } catch (error) {
                alert('Ошибка сохранения');
            }
        },
        async toggleBlock() {
            try {
                const action = this.user.is_blocked ? 'unblock' : 'block';
                await axios.post(`/admin/users/${this.user.id}/${action}`);
                this.user.is_blocked = !this.user.is_blocked;
            } catch (error) {
                alert('Ошибка');
            }
        },
        async verifyEmail() {
            try {
                await axios.post(`/admin/users/${this.user.id}/verify-email`);
                alert('Почта подтверждена');
            } catch (error) {
                alert('Ошибка');
            }
        },
        async resetPassword() {
            if (this.newPassword !== this.newPasswordConfirm) return alert('Пароли не совпадают');
            try {
                await axios.post(`/admin/users/${this.user.id}/reset-password`, {
                    password: this.newPassword,
                    password_confirmation: this.newPasswordConfirm
                });
                alert('Пароль сброшен');
                this.showResetPassword = false;
                this.newPassword = '';
            } catch (error) {
                alert('Ошибка сброса');
            }
        },
        async deleteUser() {
            if (!confirm('Удалить пользователя без возможности восстановления?')) return;
            try {
                await axios.delete(`/admin/users/${this.user.id}`);
                alert('Пользователь удалён');
                this.$router.push('/backoffice/users');
            } catch (error) {
                alert('Ошибка удаления');
            }
        }
    }
}
</script>
