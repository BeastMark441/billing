<template>
    <div class="flex items-center justify-center min-h-[80vh]">
        <div class="w-full max-w-md">
            <div class="glass-card p-8 rounded-2xl relative overflow-hidden">
                <!-- Background Glow -->
                <div class="absolute top-0 left-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl -translate-y-1/2 -translate-x-1/2"></div>

                <h2 class="text-3xl font-bold mb-8 text-center bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                    Создать аккаунт
                </h2>
                
                <form @submit.prevent="register" class="space-y-6 relative z-10">
                    <div v-if="errorMessage" class="text-sm text-red-400 bg-red-500/10 border border-red-500/30 rounded-xl px-4 py-3">
                        {{ errorMessage }}
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Имя пользователя (Логин)</label>
                        <input v-model="name" type="text" 
                            class="w-full bg-[#0a0a0f]/50 border border-white/10 rounded-xl px-4 py-3 focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none transition text-white placeholder-gray-600"
                            placeholder="username"
                            required>
                        <p v-if="errors.name" class="text-red-400 text-xs mt-1">{{ errors.name[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input v-model="email" type="email" 
                            class="w-full bg-[#0a0a0f]/50 border border-white/10 rounded-xl px-4 py-3 focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none transition text-white placeholder-gray-600"
                            placeholder="name@example.com"
                            required>
                        <p v-if="errors.email" class="text-red-400 text-xs mt-1">{{ errors.email[0] }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Пароль</label>
                        <input v-model="password" type="password" 
                            class="w-full bg-[#0a0a0f]/50 border border-white/10 rounded-xl px-4 py-3 focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none transition text-white placeholder-gray-600"
                            placeholder="••••••••"
                            required>
                        <p v-if="errors.password" class="text-red-400 text-xs mt-1">{{ errors.password[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Подтверждение пароля</label>
                        <input v-model="password_confirmation" type="password" 
                            class="w-full bg-[#0a0a0f]/50 border border-white/10 rounded-xl px-4 py-3 focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none transition text-white placeholder-gray-600"
                            placeholder="••••••••"
                            required>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:shadow-primary/40 transition">
                        Зарегистрироваться
                    </button>
                </form>
                
                <p class="mt-8 text-center text-sm text-gray-400">
                    Уже есть аккаунт? 
                    <router-link to="/login" class="text-primary hover:text-green-400 font-medium transition">Войти</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            errors: {},
            errorMessage: ''
        }
    },
    methods: {
        async register() {
            this.errors = {};
            try {
                const response = await axios.post('/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                });
                localStorage.setItem('token', response.data.access_token);
                this.$router.push('/dashboard');
                window.location.reload();
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    this.errorMessage = 'Ошибка регистрации: ' + (error.response?.data?.message || error.message);
                }
            }
        }
    }
}
</script>
