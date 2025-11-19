import { createRouter, createWebHistory } from 'vue-router';
import DashboardApp from '../components/admin/DashboardApp.vue';
import CategoriesApp from '../components/admin/CategoriesApp.vue';
import ProductsApp from '../components/admin/ProductsApp.vue';
import ProductFormApp from '../components/admin/ProductFormApp.vue';
import BrandsApp from '../components/admin/BrandsApp.vue';
import OrdersApp from '../components/admin/OrdersApp.vue';
import CustomersApp from '../components/admin/CustomersApp.vue';
import SettingsApp from '../components/admin/SettingsApp.vue';

const routes = [
    {
        path: '/admin',
        name: 'Dashboard',
        component: DashboardApp,
    },
    {
        path: '/admin/categories',
        name: 'Categories',
        component: CategoriesApp,
    },
    {
        path: '/admin/products',
        name: 'Products',
        component: ProductsApp,
    },
    {
        path: '/admin/products/create',
        name: 'ProductCreate',
        component: ProductFormApp,
    },
    {
        path: '/admin/products/:id/edit',
        name: 'ProductEdit',
        component: ProductFormApp,
    },
    {
        path: '/admin/brands',
        name: 'Brands',
        component: BrandsApp,
    },
    {
        path: '/admin/orders',
        name: 'Orders',
        component: OrdersApp,
    },
    {
        path: '/admin/customers',
        name: 'Customers',
        component: CustomersApp,
    },
    {
        path: '/admin/settings',
        name: 'Settings',
        component: SettingsApp,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Проверка авторизации перед каждым переходом
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('admin_token');
    
    if (!token && to.path.startsWith('/admin')) {
        window.location.href = '/auth';
        return;
    }
    
    next();
});

export default router;
