import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Dashboard from '../pages/Dashboard.vue';
import Products from '../pages/Products.vue';
import AdminDashboard from '../pages/AdminDashboard.vue';
import AdminUsers from '../pages/admin/Users.vue';
import AdminUserShow from '../pages/admin/UserShow.vue';
import AdminServers from '../pages/admin/Servers.vue';
import AdminTickets from '../pages/admin/tickets/Index.vue';
import AdminCoupons from '../pages/admin/Coupons.vue';
import AdminTrials from '../pages/admin/Trials.vue';
import AdminCategories from '../pages/admin/Categories.vue';
import Services from '../pages/Services.vue';
import Contacts from '../pages/Contacts.vue';
import Legal from '../pages/Legal.vue';
import ServerControl from '../pages/ServerControl.vue';

const routes = [
    { path: '/', component: Home, name: 'Home' },
    { path: '/services', component: Services, name: 'Services' },
    { path: '/contacts', component: Contacts, name: 'Contacts' },
    { path: '/legal', component: Legal, name: 'Legal' },
    { path: '/login', component: Login, name: 'Login', meta: { guest: true } },
    { path: '/register', component: Register, name: 'Register', meta: { guest: true } },
    { 
        path: '/dashboard', 
        component: Dashboard, 
        name: 'Dashboard',
        meta: { requiresAuth: true }
    },
    { 
        path: '/products', 
        component: Products, 
        name: 'Products',
        meta: { requiresAuth: true }
    },
    {
        path: '/catalog',
        component: Products,
        name: 'Catalog'
    },
    { 
        path: '/servers/:id', 
        component: ServerControl, 
        name: 'ServerControl',
        meta: { requiresAuth: true }
    },
    { 
        path: '/admin', 
        component: AdminDashboard, 
        name: 'Admin',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: '/admin/users', 
        component: AdminUsers, 
        name: 'AdminUsers',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: '/admin/users/:id', 
        component: AdminUserShow, 
        name: 'AdminUserShow',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: '/admin/servers', 
        component: AdminServers, 
        name: 'AdminServers',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: '/admin/tickets', 
        component: AdminTickets, 
        name: 'AdminTickets',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: '/admin/coupons', 
        component: AdminCoupons, 
        name: 'AdminCoupons',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: '/admin/trials', 
        component: AdminTrials, 
        name: 'AdminTrials',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    { 
        path: '/admin/categories', 
        component: AdminCategories, 
        name: 'AdminCategories',
        meta: { requiresAuth: true, requiresAdmin: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    }
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role');

    if (to.meta.requiresAuth && !token) {
        next('/login');
    } else if (to.meta.requiresAdmin && role !== 'admin') {
        next('/dashboard');
    } else if (to.meta.guest && token) {
        next('/services');
    } else {
        next();
    }
});

export default router;
