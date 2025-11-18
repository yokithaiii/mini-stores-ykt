import './bootstrap';
import { createApp } from 'vue';
import router from './router';
import App from './App.vue';
import DashboardApp from './components/admin/DashboardApp.vue';
import CategoriesApp from './components/admin/CategoriesApp.vue';
import ProductsApp from './components/admin/ProductsApp.vue';
import OrdersApp from './components/admin/OrdersApp.vue';
import BrandsApp from './components/admin/BrandsApp.vue';
import AuthApp from './components/AuthApp.vue';

const mountApp = (id, component, propsResolver = () => ({})) => {
    const root = document.getElementById(id);
    if (!root) {
        return;
    }

    const props = propsResolver(root);
    const app = createApp(component, props);
    app.mount(root);
};

document.addEventListener('DOMContentLoaded', () => {
    // Главное SPA приложение с роутером
    const appRoot = document.getElementById('app');
    if (appRoot) {
        const app = createApp(App);
        app.use(router);
        app.mount('#app');
    }
    
    // Админ-панель (без роутера)
    mountApp('auth-app', AuthApp, (root) => ({
        adminUrl: root.dataset.adminUrl,
    }));

    mountApp('dashboard-app', DashboardApp);
    mountApp('categories-app', CategoriesApp);
    mountApp('products-app', ProductsApp);
    mountApp('orders-app', OrdersApp);
    mountApp('brands-app', BrandsApp);
});
