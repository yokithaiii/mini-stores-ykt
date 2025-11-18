import { createRouter, createWebHistory } from 'vue-router';
import ShopApp from '../components/ShopApp.vue';
import ProductDetailApp from '../components/ProductDetailApp.vue';
import CustomerProfileApp from '../components/CustomerProfileApp.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: ShopApp,
    },
    {
        path: '/product/:id',
        name: 'product',
        component: ProductDetailApp,
        props: true,
    },
    {
        path: '/profile',
        name: 'profile',
        component: CustomerProfileApp,
        meta: { requiresAuth: true },
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
    },
});

// Проверка авторизации для защищенных роутов
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth) {
        const customerPhone = localStorage.getItem('customer_phone');
        if (!customerPhone) {
            next('/');
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
