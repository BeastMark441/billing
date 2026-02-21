import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

axios.defaults.baseURL = '/api';
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

axios.interceptors.response.use(
    response => response,
    error => {
        const status = error?.response?.status;
        if (status === 401 || status === 419) {
            localStorage.removeItem('token');
            localStorage.removeItem('role');
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        } else if (status === 403) {
            alert('Недостаточно прав для выполнения действия');
        }
        return Promise.reject(error);
    }
);

const app = createApp(App);
app.use(router);
app.mount('#app');
