<template>
    <div class="space-y-8">
        <!-- Header -->
        <div class="glass-card p-6 rounded-2xl flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white">Личный кабинет</h1>
                <p class="text-gray-400">Управление аккаунтом и услугами</p>
            </div>
            <div class="glass-card p-4 rounded-xl border border-white/5 flex items-center gap-6">
                <div>
                    <span class="text-gray-400 text-xs uppercase tracking-wider block mb-1">Баланс</span>
                    <span class="text-3xl font-bold text-primary">{{ balance }}₽</span>
                </div>
                <button @click="showTopUpModal = true" class="btn-primary p-3 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex overflow-x-auto gap-2 pb-2 border-b border-white/5">
            <button @click="activeTab = 'overview'" 
                :class="activeTab === 'overview' ? 'bg-primary/10 text-primary border-primary' : 'text-gray-400 border-transparent hover:text-white hover:bg-white/5'"
                class="px-5 py-2 rounded-lg text-sm font-bold transition border">
                Обзор
            </button>
            <button @click="activeTab = 'settings'" 
                :class="activeTab === 'settings' ? 'bg-primary/10 text-primary border-primary' : 'text-gray-400 border-transparent hover:text-white hover:bg-white/5'"
                class="px-5 py-2 rounded-lg text-sm font-bold transition border">
                Настройки
            </button>
            <button @click="activeTab = 'support'" 
                :class="activeTab === 'support' ? 'bg-primary/10 text-primary border-primary' : 'text-gray-400 border-transparent hover:text-white hover:bg-white/5'"
                class="px-5 py-2 rounded-lg text-sm font-bold transition border">
                Поддержка
            </button>
        </div>

        <!-- OVERVIEW TAB -->
        <div v-if="activeTab === 'overview'" class="space-y-8 animate-fade-in">
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <router-link to="/products" class="glass-card p-4 rounded-xl border border-white/5 hover:border-primary/50 hover:bg-white/5 transition group">
                    <div class="mb-3 flex items-center justify-center w-10 h-10 rounded-lg bg-white/5 group-hover:bg-primary/20">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 7h13M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="font-bold text-white">Новый заказ</div>
                    <div class="text-xs text-gray-400 mt-1">Арендовать сервер</div>
                </router-link>
                <button @click="showTopUpModal = true" class="glass-card p-4 rounded-xl border border-white/5 hover:border-primary/50 hover:bg-white/5 transition text-left group">
                    <div class="mb-3 flex items-center justify-center w-10 h-10 rounded-lg bg-white/5 group-hover:bg-primary/20">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                            <path d="M2 10h20" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="font-bold text-white">Пополнить</div>
                    <div class="text-xs text-gray-400 mt-1">Баланс кошелька</div>
                </button>
                <button @click="activeTab = 'support'" class="glass-card p-4 rounded-xl border border-white/5 hover:border-primary/50 hover:bg-white/5 transition text-left group">
                    <div class="mb-3 flex items-center justify-center w-10 h-10 rounded-lg bg-white/5 group-hover:bg-primary/20">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="14" rx="2" stroke-width="2"/>
                            <path d="M8 8h8M8 12h5" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="font-bold text-white">Поддержка</div>
                    <div class="text-xs text-gray-400 mt-1">Открыть тикет</div>
                </button>
                <a href="https://discord.gg" target="_blank" class="glass-card p-4 rounded-xl border border-white/5 hover:border-primary/50 hover:bg-white/5 transition group">
                    <div class="mb-3 flex items-center justify-center w-10 h-10 rounded-lg bg-white/5 group-hover:bg-primary/20">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M7 17L17 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 7h10v10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="font-bold text-white">Сообщество</div>
                    <div class="text-xs text-gray-400 mt-1">Discord сервер</div>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Active Servers -->
                <div class="lg:col-span-2 glass-card rounded-2xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold flex items-center gap-2">
                            <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                            Мои серверы
                        </h2>
                    </div>
                    
                    <div v-if="servers.length === 0" class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-800 rounded-xl">
                        <p class="mb-4">У вас пока нет активных серверов</p>
                        <router-link to="/products" class="btn-primary px-4 py-2 rounded-lg text-sm">Перейти в каталог</router-link>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div v-for="server in servers" :key="server.id" class="bg-black/20 p-4 rounded-xl border border-white/5 hover:border-primary/30 transition group flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-4 w-full">
                                <div class="w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center group-hover:bg-primary/20 transition duration-300 border border-white/10">
                                    <svg class="w-6 h-6 text-gray-300 group-hover:text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="3" y="4" width="18" height="14" rx="2" stroke-width="2"/>
                                        <path d="M7 8h4M13 8h4M7 12h10M7 16h6" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">{{ server.name }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400">
                                        <span class="bg-gray-800 px-2 py-0.5 rounded text-xs font-mono border border-white/5">{{ server.ip }}:{{ server.port }}</span>
                                        <span :class="{'text-green-400': server.status === 'active', 'text-red-400': server.status !== 'active'}" class="text-xs uppercase font-bold tracking-wider flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="{'bg-green-400': server.status === 'active', 'bg-red-400': server.status !== 'active'}"></span>
                                            {{ server.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <router-link :to="'/servers/' + server.id" class="w-full md:w-auto text-center bg-white/5 hover:bg-white/10 text-white px-6 py-2 rounded-lg text-sm transition border border-white/10 hover:border-white/30 font-medium">
                                Управление
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="glass-card rounded-2xl p-6 h-fit">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-primary rounded-full"></span>
                        История операций
                    </h2>
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        <div v-for="payment in history" :key="payment.id" class="flex justify-between items-center border-b border-gray-800 pb-3 last:border-0 hover:bg-white/5 p-2 rounded transition">
                            <div>
                                <div class="font-bold text-sm">{{ payment.amount > 0 ? 'Пополнение' : 'Оплата услуг' }}</div>
                                <div class="text-xs text-gray-500">{{ new Date(payment.created_at).toLocaleDateString() }}</div>
                            </div>
                            <span :class="{'text-red-400': payment.amount < 0, 'text-green-400': payment.amount > 0}" class="font-mono font-bold">
                                {{ payment.amount > 0 ? '+' : '' }}{{ payment.amount }}₽
                            </span>
                        </div>
                        <div v-if="history.length === 0" class="text-center text-gray-500 text-sm py-4">
                            История пуста
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SETTINGS TAB -->
        <div v-if="activeTab === 'settings'" class="glass-card p-8 rounded-2xl animate-fade-in max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">Настройки аккаунта</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Personal Info -->
                <form @submit.prevent="updateProfileInfo" class="space-y-6">
                    <h3 class="text-lg font-bold text-white border-b border-white/10 pb-2">Личные данные</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">Фамилия</label>
                            <input v-model="user.last_name" maxlength="50" class="input-field w-full px-4 py-3 rounded-xl text-white">
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">Имя</label>
                            <input v-model="user.first_name" maxlength="50" class="input-field w-full px-4 py-3 rounded-xl text-white">
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">Отчество</label>
                            <input v-model="user.middle_name" maxlength="50" class="input-field w-full px-4 py-3 rounded-xl text-white">
                        </div>
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm mb-2 block">Телефон</label>
                        <input v-model="user.phone" maxlength="20" @input="filterPhone" class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="+7 (999) 000-00-00">
                    </div>

                    <h3 class="text-lg font-bold text-white border-b border-white/10 pb-2 pt-4">Соцсети (Опционально)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">Telegram</label>
                            <input v-model="user.telegram" maxlength="32" class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="@username">
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">VK</label>
                            <input v-model="user.vk" maxlength="50" class="input-field w-full px-4 py-3 rounded-xl text-white" placeholder="id123456">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg font-bold shadow-lg shadow-primary/20 text-sm">
                            Сохранить данные
                        </button>
                    </div>
                </form>

                <!-- Security -->
                <form @submit.prevent="updatePassword" class="space-y-6">
                    <h3 class="text-lg font-bold text-white border-b border-white/10 pb-2">Безопасность</h3>
                    <div>
                        <label class="text-gray-400 text-sm mb-2 block">Email</label>
                        <input type="email" :value="user?.email" disabled class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed">
                        <p class="text-xs text-gray-600 mt-1">Для смены email обратитесь в поддержку</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">Текущий пароль</label>
                            <input v-model="passwordForm.current_password" type="password" class="input-field w-full px-4 py-3 rounded-xl text-white placeholder-gray-600" required>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">Новый пароль</label>
                            <input v-model="passwordForm.password" type="password" class="input-field w-full px-4 py-3 rounded-xl text-white placeholder-gray-600" required>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm mb-2 block">Повторите пароль</label>
                            <input v-model="passwordForm.password_confirmation" type="password" class="input-field w-full px-4 py-3 rounded-xl text-white placeholder-gray-600" required>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg font-bold shadow-lg shadow-primary/20 text-sm">
                            Обновить пароль
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SUPPORT TAB -->
        <div v-if="activeTab === 'support'" class="glass-card p-8 rounded-2xl animate-fade-in">
            <div v-if="!viewingTicket">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Ваши обращения</h2>
                    <button @click="showNewTicketModal = true" class="btn-primary px-4 py-2 rounded-lg text-sm">
                        + Создать тикет
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div v-if="tickets.length === 0" class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-800 rounded-xl">
                        <p>У вас нет открытых обращений</p>
                    </div>
                    <div v-for="ticket in tickets" :key="ticket.id" @click="openTicket(ticket)" class="bg-black/20 p-4 rounded-xl border border-white/5 hover:border-primary/30 transition cursor-pointer flex justify-between items-center group">
                        <div>
                            <h3 class="font-bold text-lg group-hover:text-primary transition">{{ ticket.subject }}</h3>
                            <div class="text-xs text-gray-400 flex gap-2 mt-1">
                                <span>#{{ ticket.id }}</span>
                                <span>•</span>
                                <span>{{ new Date(ticket.updated_at).toLocaleString() }}</span>
                                <span v-if="ticket.category">• {{ ticket.category }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span :class="{
                                'text-green-400 bg-green-400/10': (ticket.status_v2 || ticket.status) === 'open',
                                'text-yellow-400 bg-yellow-400/10': ticket.status_v2 === 'in_progress' || ticket.status_v2 === 'waiting',
                                'text-blue-400 bg-blue-400/10': ticket.status_v2 === 'resolved',
                                'text-gray-400 bg-gray-400/10': (ticket.status_v2 || ticket.status) === 'closed'
                            }" class="px-2 py-1 rounded text-xs uppercase font-bold">
                                {{ ticket.status_v2 || ticket.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Single Ticket View -->
            <div v-else class="h-[600px] flex flex-col">
                <div class="flex items-center gap-4 mb-4 border-b border-white/10 pb-4">
                    <button @click="viewingTicket = null" class="text-gray-400 hover:text-white">← Назад</button>
                    <div>
                        <h2 class="text-xl font-bold">{{ viewingTicket.subject }}</h2>
                        <span class="text-xs text-gray-400">Тикет #{{ viewingTicket.id }}</span>
                    </div>
                </div>
                
                <div class="flex-grow overflow-y-auto space-y-4 mb-4 pr-2 custom-scrollbar">
                    <div v-for="msg in viewingTicket.messages" :key="msg.id" 
                        :class="msg.user_id === user.id ? 'ml-auto bg-primary/10 border-primary/20' : 'mr-auto bg-gray-800/50 border-white/10'"
                        class="max-w-[80%] p-4 rounded-xl border">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-sm" :class="msg.user_id === user.id ? 'text-primary' : 'text-gray-300'">
                                {{ msg.user_id === user.id ? 'Вы' : 'Поддержка' }}
                            </span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">
                                    {{ new Date(msg.created_at).toLocaleString() }}
                                    <span v-if="msg.edited_at" class="text-[10px] text-gray-400">• изм. {{ new Date(msg.edited_at).toLocaleString() }}</span>
                                </span>
                                <button v-if="canEditClient(msg)" @click="startEditClient(msg)" class="text-xs text-gray-400 hover:text-white">Ред.</button>
                                <button v-if="canEditClient(msg)" @click="deleteClientMsg(msg)" class="text-xs text-red-400 hover:text-red-300">Удалить</button>
                            </div>
                        </div>
                        <div v-if="editingClientMessage && editingClientMessage.id === msg.id">
                            <textarea v-model="clientEditText" class="input-field w-full px-3 py-2 rounded text-sm"></textarea>
                            <div class="flex gap-2 mt-2">
                                <button @click="confirmEditClient(msg)" class="btn-primary px-3 py-1 rounded text-xs">Сохранить</button>
                                <button @click="cancelEditClient" class="text-gray-400 hover:text-white text-xs">Отмена</button>
                            </div>
                        </div>
                        <p v-else class="text-sm whitespace-pre-wrap">{{ msg.message }}</p>
                        <div v-if="msg.attachments && msg.attachments.length" class="flex gap-2 mt-2 flex-wrap">
                            <a v-for="att in msg.attachments" :key="att.id" :href="`/storage/${att.path}`" target="_blank" class="text-xs text-primary hover:underline">
                                {{ att.original_name }}
                            </a>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="replyTicket" class="space-y-2">
                    <input v-model="replyMessage" placeholder="Напишите сообщение..." class="input-field w-full px-4 py-3 rounded-xl text-white">
                    <div class="flex items-center justify-between">
                        <input type="file" multiple @change="handleReplyFiles" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" class="text-xs">
                        <button @click.prevent="deleteTicketClient" class="text-red-400 hover:text-red-300 text-sm">Удалить тикет</button>
                    </div>
                    <div v-if="replyAttachments.length" class="flex gap-2 flex-wrap">
                        <img v-for="(f, idx) in clientImagePreviews" :key="idx" :src="f" class="w-16 h-16 object-cover rounded border border-white/10" />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary px-6 py-3 rounded-xl">Отправить</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Top Up Modal -->
        <div v-if="showTopUpModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="glass-card p-8 rounded-2xl w-full max-w-md relative transform transition-all scale-100">
                <button @click="showTopUpModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                    <span class="bg-primary/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                            <path d="M2 10h20" stroke-width="2"/>
                        </svg>
                    </span>
                    Пополнение баланса
                </h2>
                
                <form @submit.prevent="initiatePayment" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Сумма пополнения (₽)</label>
                        <div class="relative">
                            <input v-model="topUpAmount" type="number" min="10" 
                                class="w-full bg-[#0a0a0f]/50 border border-white/10 rounded-xl pl-4 pr-12 py-4 focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none transition text-white text-2xl font-bold font-mono"
                                placeholder="0" required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">RUB</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" @click="topUpAmount = 100" class="bg-white/5 hover:bg-primary/20 hover:text-primary hover:border-primary/30 border border-white/5 py-2 rounded-lg text-sm font-bold transition">100₽</button>
                        <button type="button" @click="topUpAmount = 500" class="bg-white/5 hover:bg-primary/20 hover:text-primary hover:border-primary/30 border border-white/5 py-2 rounded-lg text-sm font-bold transition">500₽</button>
                        <button type="button" @click="topUpAmount = 1000" class="bg-white/5 hover:bg-primary/20 hover:text-primary hover:border-primary/30 border border-white/5 py-2 rounded-lg text-sm font-bold transition">1000₽</button>
                    </div>

                    <button type="submit" :disabled="processing" class="w-full btn-primary py-4 rounded-xl font-bold shadow-lg shadow-primary/20 flex justify-center items-center gap-2 group">
                        <span v-if="!processing">Перейти к оплате</span>
                        <span v-if="!processing" class="group-hover:translate-x-1 transition">→</span>
                        <span v-else>Обработка...</span>
                    </button>
                </form>
                
                <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Безопасный платеж через TBank
                </div>
            </div>
        </div>

        <!-- Create Ticket Modal -->
        <div v-if="showNewTicketModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="glass-card p-8 rounded-2xl w-full max-w-lg">
                <h3 class="text-2xl font-bold mb-6">Новое обращение</h3>
                <form @submit.prevent="createTicket" class="space-y-4">
                    <input v-model="newTicket.subject" placeholder="Тема обращения" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <input v-model="newTicket.category" placeholder="Категория (например: оплата, серверы)" class="input-field w-full px-4 py-3 rounded-xl text-white" required>
                    <select v-model="newTicket.priority" class="input-field w-full px-4 py-3 rounded-xl text-white">
                        <option value="low">Низкий приоритет</option>
                        <option value="medium">Средний приоритет</option>
                        <option value="high">Высокий приоритет</option>
                    </select>
                    <textarea v-model="newTicket.message" placeholder="Опишите вашу проблему..." class="input-field w-full px-4 py-3 rounded-xl text-white h-32" required></textarea>
                    <input type="file" multiple @change="handleCreateFiles" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" class="text-xs">
                    <div v-if="createImagePreviews.length" class="flex gap-2 flex-wrap">
                        <img v-for="(f, idx) in createImagePreviews" :key="idx" :src="f" class="w-16 h-16 object-cover rounded border border-white/10" />
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showNewTicketModal = false" class="text-gray-400 hover:text-white px-4">Отмена</button>
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
            activeTab: 'overview',
            balance: 0,
            servers: [],
            history: [],
            user: {
                first_name: '', last_name: '', middle_name: '', phone: '', telegram: '', vk: ''
            },
            showTopUpModal: false,
            topUpAmount: 100,
            processing: false,
            passwordForm: {
                current_password: '',
                password: '',
                password_confirmation: ''
            },
            // Tickets
            tickets: [],
            viewingTicket: null,
            showNewTicketModal: false,
            newTicket: { subject: '', category: '', message: '', priority: 'medium' },
            createAttachments: [],
            createImagePreviews: [],
            replyMessage: '',
            replyAttachments: [],
            clientImagePreviews: [],
            editingClientMessage: null,
            clientEditText: ''
        }
    },
    async mounted() {
        // Check for payment success/fail params
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('payment') === 'success') {
            alert('Оплата прошла успешно! Баланс пополнен.');
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        await this.fetchData();
        await this.fetchUser();
        await this.fetchTickets();
    },
    methods: {
        async fetchData() {
            try {
                const balanceRes = await axios.get('/client/balance');
                this.balance = balanceRes.data.balance;
                this.history = balanceRes.data.history;

                const serversRes = await axios.get('/client/servers');
                this.servers = serversRes.data;
            } catch (error) {
                console.error(error);
            }
        },
        async fetchUser() {
            try {
                const res = await axios.get('/user');
                this.user = res.data;
            } catch (error) {
                console.error(error);
            }
        },
        async initiatePayment() {
            this.processing = true;
            try {
                const response = await axios.post('/client/balance/topup', {
                    amount: this.topUpAmount
                });
                window.location.href = response.data.url;
            } catch (error) {
                alert('Ошибка создания платежа: ' + (error.response?.data?.error || error.message));
                this.processing = false;
            }
        },
        async updateProfileInfo() {
            try {
                const res = await axios.put('/user/profile', {
                    first_name: this.user.first_name,
                    last_name: this.user.last_name,
                    middle_name: this.user.middle_name,
                    phone: this.user.phone,
                    telegram: this.user.telegram,
                    vk: this.user.vk
                });
                this.user = res.data;
                alert('Данные обновлены');
            } catch (error) {
                alert('Ошибка обновления: ' + (error.response?.data?.message || error.message));
            }
        },
        filterPhone(event) {
            this.user.phone = event.target.value.replace(/[^0-9+]/g, '');
        },
        async updatePassword() {
            try {
                await axios.post('/user/password', this.passwordForm);
                alert('Пароль успешно изменен!');
                this.passwordForm = { current_password: '', password: '', password_confirmation: '' };
            } catch (error) {
                alert(error.response?.data?.message || 'Ошибка');
            }
        },
        // Tickets
        async fetchTickets() {
            try {
                const res = await axios.get('/client/tickets');
                this.tickets = res.data;
            } catch (error) {
                console.error(error);
            }
        },
        async createTicket() {
            try {
                const fd = new FormData();
                fd.append('subject', this.newTicket.subject);
                fd.append('category', this.newTicket.category);
                fd.append('priority', this.newTicket.priority);
                fd.append('message', this.newTicket.message);
                this.createAttachments.forEach(f => fd.append('attachments[]', f));
                await axios.post('/client/tickets', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
                this.showNewTicketModal = false;
                this.newTicket = { subject: '', category: '', message: '', priority: 'medium' };
                this.createAttachments = [];
                this.createImagePreviews = [];
                this.fetchTickets();
            } catch (error) {
                alert('Ошибка создания тикета');
            }
        },
        async openTicket(ticket) {
            try {
                const res = await axios.get(`/client/tickets/${ticket.id}`);
                this.viewingTicket = res.data;
            } catch (error) {
                alert('Ошибка загрузки тикета');
            }
        },
        async replyTicket() {
            if (!this.replyMessage) return;
            try {
                const fd = new FormData();
                fd.append('message', this.replyMessage);
                this.replyAttachments.forEach(f => fd.append('attachments[]', f));
                await axios.post(`/client/tickets/${this.viewingTicket.id}/reply`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
                this.replyMessage = '';
                this.replyAttachments = [];
                this.clientImagePreviews = [];
                // Reload ticket messages
                const res = await axios.get(`/client/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = res.data;
                this.fetchTickets(); // Update list status
            } catch (error) {
                alert('Ошибка отправки сообщения');
            }
        },
        handleCreateFiles(e) {
            this.createAttachments = Array.from(e.target.files || []);
            this.createImagePreviews = this.createAttachments
                .filter(f => /image\/(png|jpe?g)/.test(f.type))
                .map(f => URL.createObjectURL(f));
        },
        handleReplyFiles(e) {
            this.replyAttachments = Array.from(e.target.files || []);
            this.clientImagePreviews = this.replyAttachments
                .filter(f => /image\/(png|jpe?g)/.test(f.type))
                .map(f => URL.createObjectURL(f));
        },
        canEditClient(msg) {
            if (!msg || !msg.created_at) return false;
            const created = new Date(msg.created_at);
            const diff = (Date.now() - created.getTime()) / (1000 * 60);
            return msg.user_id === this.user.id && diff <= 30;
        },
        startEditClient(msg) {
            this.editingClientMessage = msg;
            this.clientEditText = msg.message;
        },
        cancelEditClient() {
            this.editingClientMessage = null;
            this.clientEditText = '';
        },
        async confirmEditClient(msg) {
            try {
                await axios.put(`/client/messages/${msg.id}`, { message: this.clientEditText });
                const res = await axios.get(`/client/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = res.data;
                this.cancelEditClient();
            } catch (e) {
                alert('Не удалось сохранить изменения');
            }
        },
        async deleteClientMsg(msg) {
            if (!confirm('Удалить сообщение?')) return;
            try {
                await axios.delete(`/client/messages/${msg.id}`);
                const res = await axios.get(`/client/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = res.data;
            } catch (e) {
                alert('Не удалось удалить сообщение');
            }
        },
        async deleteTicketClient() {
            if (!confirm('Удалить этот тикет?')) return;
            try {
                await axios.delete(`/client/tickets/${this.viewingTicket.id}`);
                this.viewingTicket = null;
                this.fetchTickets();
            } catch (e) {
                alert('Не удалось удалить тикет');
            }
        }
    }
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
