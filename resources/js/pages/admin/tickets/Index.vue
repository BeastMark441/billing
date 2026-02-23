<template>
    <div class="space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold">Управление тикетами</h1>
            <router-link to="/backoffice" class="text-gray-400 hover:text-white text-sm">← Вернуться назад</router-link>
        </div>
        
        <div class="glass-card p-4 rounded-2xl">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                <input v-model="filters.q" placeholder="Поиск по теме/категории" class="input-field px-3 py-2 rounded-lg text-sm">
                <select v-model="filters.status_v2" class="input-field px-3 py-2 rounded-lg text-sm">
                    <option value="">Все статусы</option>
                    <option value="open">Открыт</option>
                    <option value="in_progress">В работе</option>
                    <option value="waiting">Ожидает ответа</option>
                    <option value="resolved">Решен</option>
                    <option value="closed">Закрыт</option>
                </select>
                <select v-model="filters.priority" class="input-field px-3 py-2 rounded-lg text-sm">
                    <option value="">Любой приоритет</option>
                    <option value="low">Низкий</option>
                    <option value="medium">Средний</option>
                    <option value="high">Высокий</option>
                </select>
                <input v-model="filters.category" placeholder="Категория" class="input-field px-3 py-2 rounded-lg text-sm">
                <input v-model="filters.assigned_to" placeholder="ID исполнителя" class="input-field px-3 py-2 rounded-lg text-sm">
                <button @click="fetchTickets" class="btn-primary rounded-lg text-sm">Фильтровать</button>
            </div>
        </div>
        
        <div class="glass-card p-6 rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 text-sm border-b border-white/10">
                            <th class="p-4">ID</th>
                            <th class="p-4">Тема</th>
                            <th class="p-4">Пользователь</th>
                            <th class="p-4">Приоритет</th>
                            <th class="p-4">Статус</th>
                            <th class="p-4">Категория</th>
                            <th class="p-4">Дата</th>
                            <th class="p-4">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ticket in tickets" :key="ticket.id" class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500">#{{ ticket.id }}</td>
                            <td class="p-4 font-bold">{{ ticket.subject }}</td>
                            <td class="p-4">
                                <router-link :to="`/backoffice/users/${ticket.user.id}`" class="hover:text-primary">
                                    <div>{{ ticket.user.name }}</div>
                                </router-link>
                                <div class="text-xs text-gray-500">{{ ticket.user.email }}</div>
                            </td>
                            <td class="p-4">
                                <span :class="{
                                    'text-red-400': ticket.priority === 'high',
                                    'text-yellow-400': ticket.priority === 'medium',
                                    'text-green-400': ticket.priority === 'low'
                                }" class="uppercase text-xs font-bold">{{ ticket.priority }}</span>
                            </td>
                            <td class="p-4">
                                <span :class="{
                                    'text-green-400 bg-green-400/10': ticket.status_v2 === 'open',
                                    'text-yellow-400 bg-yellow-400/10': ticket.status_v2 === 'in_progress' || ticket.status_v2 === 'waiting',
                                    'text-blue-400 bg-blue-400/10': ticket.status_v2 === 'resolved',
                                    'text-gray-400 bg-gray-400/10': ticket.status_v2 === 'closed'
                                }" class="px-2 py-1 rounded text-xs uppercase font-bold">
                                    {{ ticket.status_v2 || ticket.status }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-400 text-sm">{{ ticket.category || '—' }}</td>
                            <td class="p-4 text-gray-500 text-sm">{{ new Date(ticket.created_at).toLocaleDateString() }}</td>
                            <td class="p-4">
                                <button @click="openTicket(ticket)" class="btn-primary px-3 py-1 rounded text-xs">Открыть</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ticket View Modal -->
        <div v-if="viewingTicket" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div class="glass-card p-8 rounded-2xl w-full max-w-2xl h-[80vh] flex flex-col relative">
                <button @click="viewingTicket = null" class="absolute top-4 right-4 text-gray-400 hover:text-white">✕</button>
                
                <div class="mb-4 border-b border-white/10 pb-4">
                    <h2 class="text-xl font-bold">{{ viewingTicket.subject }}</h2>
                    <div class="grid grid-cols-2 gap-3 mt-3 text-sm">
                        <div class="text-gray-400">Тикет #{{ viewingTicket.id }}</div>
                        <div class="text-gray-400">Создан: {{ new Date(viewingTicket.created_at).toLocaleString() }}</div>
                        <div class="text-gray-400">Категория: <b class="text-white">{{ viewingTicket.category || '—' }}</b></div>
                        <div class="text-gray-400">Клиент: <router-link :to="`/backoffice/users/${viewingTicket.user.id}`" class="text-primary hover:underline">{{ viewingTicket.user.name }}</router-link></div>
                        <div class="text-gray-400">Приоритет: <b class="text-white uppercase">{{ viewingTicket.priority }}</b></div>
                        <div class="text-gray-400">Статус: <b class="text-white uppercase">{{ viewingTicket.status_v2 || viewingTicket.status }}</b></div>
                    </div>
                    <div class="flex flex-wrap gap-3 mt-3 items-end">
                        <select v-model="statusForm.status_v2" class="input-field px-2 py-1 rounded text-xs">
                            <option value="open">Открыт</option>
                            <option value="in_progress">В работе</option>
                            <option value="waiting">Ожидает ответа</option>
                            <option value="resolved">Решен</option>
                            <option value="closed">Закрыт</option>
                        </select>
                        <select v-model="statusForm.priority" class="input-field px-2 py-1 rounded text-xs">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        <input v-model="statusForm.assigned_to" placeholder="Назначить (ID)" class="input-field px-2 py-1 rounded text-xs w-32">
                        <input v-model="statusForm.category" placeholder="Категория" class="input-field px-2 py-1 rounded text-xs">
                        <button @click="applyStatus" class="btn-primary px-3 py-1 rounded text-xs">Применить</button>
                    </div>
                    <textarea v-model="statusForm.comment" placeholder="Комментарий (обязателен при смене статуса)" class="input-field mt-2 w-full px-3 py-2 rounded text-sm"></textarea>
                    <div v-if="hasServer" class="mt-4 flex flex-wrap items-center gap-3">
                        <div class="text-sm text-gray-400">
                            Связан с тарифом:
                            <span class="text-white font-bold">
                                #{{ viewingTicket.server?.id || viewingTicket.server_id }}
                                <span v-if="viewingTicket.server?.name">— {{ viewingTicket.server.name }}</span>
                            </span>
                        </div>
                        <a v-if="clientLink" :href="clientLink" target="_blank" class="btn-primary px-3 py-1 rounded text-xs">Открыть в Билинге</a>
                        <a :href="panelLink" target="_blank" class="px-3 py-1 rounded text-xs bg-white/5 hover:bg-white/10">Открыть в Панели Управления</a>
                    </div>
                </div>

                <div class="flex-grow overflow-y-auto space-y-4 mb-4 pr-2 custom-scrollbar">
                    <div v-for="msg in viewingTicket.messages" :key="msg.id" 
                        :class="msg.user_id === viewingTicket.user_id ? 'mr-auto bg-gray-800/50 border-white/10' : 'ml-auto bg-primary/10 border-primary/20'"
                        class="max-w-[80%] p-4 rounded-xl border">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-sm" :class="msg.user_id !== viewingTicket.user_id ? 'text-primary' : 'text-gray-300'">
                                {{ msg.user_id === viewingTicket.user_id ? viewingTicket.user.name : 'Support' }}
                            </span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">
                                    {{ new Date(msg.created_at).toLocaleString() }}
                                    <span v-if="msg.edited_at" class="text-[10px] text-gray-400">• изм. {{ new Date(msg.edited_at).toLocaleString() }}</span>
                                </span>
                                <button v-if="canEdit(msg)" @click="startEdit(msg)" class="text-xs text-gray-400 hover:text-white">Ред.</button>
                                <button v-if="canEdit(msg)" @click="deleteMsg(msg)" class="text-xs text-red-400 hover:text-red-300">Удалить</button>
                            </div>
                        </div>
                        <div v-if="editingMessage && editingMessage.id === msg.id">
                            <textarea v-model="editText" class="input-field w-full px-3 py-2 rounded text-sm"></textarea>
                            <div class="flex gap-2 mt-2">
                                <button @click="confirmEdit(msg)" class="btn-primary px-3 py-1 rounded text-xs">Сохранить</button>
                                <button @click="cancelEdit" class="text-gray-400 hover:text-white text-xs">Отмена</button>
                            </div>
                        </div>
                        <p v-else class="text-sm whitespace-pre-wrap">{{ msg.message }}</p>
                    </div>
                </div>

                <form @submit.prevent="replyTicket" class="space-y-2">
                    <input v-model="replyMessage" placeholder="Ответ поддержки..." class="input-field w-full px-4 py-3 rounded-xl text-white">
                    <div class="flex items-center justify-between">
                        <label class="text-xs text-gray-400 flex items-center gap-2">
                            <input type="checkbox" v-model="replyInternal"> Внутренний комментарий
                        </label>
                        <input type="file" multiple @change="handleFiles" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" class="text-xs">
                    </div>
                    <div v-if="attachments.length" class="flex gap-2 flex-wrap">
                        <img v-for="(f, idx) in imagePreviews" :key="idx" :src="f" class="w-16 h-16 object-cover rounded border border-white/10" />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary px-6 py-3 rounded-xl">Отправить</button>
                        <button @click.prevent="deleteTicket" class="text-red-400 hover:text-red-300 text-sm">Удалить тикет</button>
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
            tickets: [],
            viewingTicket: null,
            replyMessage: '',
            replyInternal: false,
            attachments: [],
            imagePreviews: [],
            editingMessage: null,
            editText: '',
            filters: { q: '', status_v2: '', priority: '', category: '', assigned_to: '' },
            statusForm: { status_v2: 'open', priority: 'medium', assigned_to: '', category: '', comment: '' }
        }
    },
    computed: {
        hasServer() {
            return !!(this.viewingTicket && (this.viewingTicket.server_id || this.viewingTicket.server));
        },
        clientLink() {
            if (!this.viewingTicket) return '';
            const id = this.viewingTicket.server_id || (this.viewingTicket.server && this.viewingTicket.server.id);
            if (!id) return '';
            return this.$router.resolve({ name: 'ServerControl', params: { id } }).href;
        },
        panelLink() {
            if (this.viewingTicket && this.viewingTicket.server && this.viewingTicket.server.panel_url) {
                return this.viewingTicket.server.panel_url;
            }
            return 'https://panel.nodeum.ru';
        }
    },
    async mounted() {
        this.fetchTickets();
    },
    methods: {
        async fetchTickets() {
            try {
                const res = await axios.get('/admin/tickets', { params: this.filters });
                this.tickets = res.data;
            } catch (error) {
                console.error(error);
            }
        },
        async openTicket(ticket) {
            try {
                const res = await axios.get(`/admin/tickets/${ticket.id}`);
                this.viewingTicket = res.data;
                this.statusForm.status_v2 = this.viewingTicket.status_v2 || 'open';
                this.statusForm.priority = this.viewingTicket.priority || 'medium';
                this.statusForm.assigned_to = this.viewingTicket.assigned_to || '';
                this.statusForm.category = this.viewingTicket.category || '';
            } catch (error) {
                alert('Error loading ticket');
            }
        },
        canEdit(msg) {
            if (!msg || !msg.created_at) return false;
            const created = new Date(msg.created_at);
            const diff = (Date.now() - created.getTime()) / (1000 * 60);
            return diff <= 30 && this.viewingTicket && msg.user_id !== this.viewingTicket.user_id; // редактировать можно свои сообщения поддержки; сервер дополнительно проверяет автора
        },
        startEdit(msg) {
            this.editingMessage = msg;
            this.editText = msg.message;
        },
        cancelEdit() {
            this.editingMessage = null;
            this.editText = '';
        },
        async confirmEdit(msg) {
            try {
                await axios.put(`/admin/messages/${msg.id}`, { message: this.editText });
                const res = await axios.get(`/admin/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = res.data;
                this.cancelEdit();
            } catch (e) {
                alert('Не удалось сохранить изменения');
            }
        },
        async deleteMsg(msg) {
            if (!confirm('Удалить сообщение?')) return;
            try {
                await axios.delete(`/admin/messages/${msg.id}`);
                const res = await axios.get(`/admin/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = res.data;
            } catch (e) {
                alert('Не удалось удалить сообщение');
            }
        },
        async applyStatus() {
            if (!this.statusForm.comment) {
                alert('Комментарий обязателен при смене статуса');
                return;
            }
            try {
                await axios.put(`/admin/tickets/${this.viewingTicket.id}`, this.statusForm);
                const res = await axios.get(`/admin/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = res.data;
                this.fetchTickets();
                this.statusForm.comment = '';
            } catch (e) {
                alert('Не удалось применить изменения статуса');
            }
        },
        handleFiles(e) {
            this.attachments = Array.from(e.target.files || []);
            this.imagePreviews = this.attachments
                .filter(f => /image\/(png|jpe?g)/.test(f.type))
                .map(f => URL.createObjectURL(f));
        },
        async replyTicket() {
            if (!this.replyMessage) return;
            try {
                const fd = new FormData();
                fd.append('message', this.replyMessage);
                fd.append('is_internal', this.replyInternal ? 1 : 0);
                this.attachments.forEach(f => fd.append('attachments[]', f));
                await axios.post(`/admin/tickets/${this.viewingTicket.id}/reply`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
                this.replyMessage = '';
                this.attachments = [];
                this.imagePreviews = [];
                // Reload ticket messages
                const res = await axios.get(`/admin/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = res.data;
                this.fetchTickets();
            } catch (error) {
                alert('Reply failed');
            }
        },
        async deleteTicket() {
            if (!confirm('Удалить этот тикет?')) return;
            try {
                await axios.delete(`/admin/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = null;
                this.fetchTickets();
            } catch (e) {
                alert('Не удалось удалить тикет');
            }
        }
    }
}
</script>
