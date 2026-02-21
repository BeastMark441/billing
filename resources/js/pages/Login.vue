<template>
    <div class="flex items-center justify-center min-h-[80vh]">
        <div class="w-full max-w-md">
            <div class="glass-card p-8 rounded-2xl relative overflow-hidden">
                <!-- Background Glow -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <h2 class="text-3xl font-bold mb-8 text-center bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                    С возвращением
                </h2>
                
                <form @submit.prevent="login" class="space-y-6 relative z-10">
                    <div v-if="errorMessage" class="text-sm text-red-400 bg-red-500/10 border border-red-500/30 rounded-xl px-4 py-3">
                        {{ errorMessage }}
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input v-model="email" type="email" 
                            class="w-full bg-[#0a0a0f]/50 border border-white/10 rounded-xl px-4 py-3 focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none transition text-white placeholder-gray-600"
                            placeholder="name@example.com"
                            required>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-400">Пароль</label>
                            <a href="#" class="text-xs text-primary hover:text-green-400 transition">Забыли пароль?</a>
                        </div>
                        <input v-model="password" type="password" 
                            class="w-full bg-[#0a0a0f]/50 border border-white/10 rounded-xl px-4 py-3 focus:border-primary/50 focus:ring-1 focus:ring-primary/50 outline-none transition text-white placeholder-gray-600"
                            placeholder="••••••••"
                            required>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl font-bold text-lg shadow-lg shadow-primary/20 hover:shadow-primary/40 transition">
                        Войти
                    </button>
                </form>
                
                <p class="mt-8 text-center text-sm text-gray-400">
                    Нет аккаунта? 
                    <router-link to="/register" class="text-primary hover:text-green-400 font-medium transition">Создать аккаунт</router-link>
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
            email: '',
            password: '',
            errorMessage: ''
        }
    },
    methods: {
        async login() {
            try {
                const response = await axios.post('/login', {
                    email: this.email,
                    password: this.password
                });
                localStorage.setItem('token', response.data.access_token);
                localStorage.setItem('role', response.data.role);
                const next = this.$route.query.next;
                this.$router.push(next || '/dashboard');
                window.location.reload(); // Force header update
            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'Ошибка входа. Пожалуйста, проверьте ваши данные.';
            }
        }
    }
}
</script>
