import './bootstrap';
import { createApp } from 'vue';
import router from './router';
import App from './App.vue';
import AuthApp from './components/AuthApp.vue';

document.addEventListener('DOMContentLoaded', () => {
    // Главное SPA приложение с роутером (магазин)
    const appRoot = document.getElementById('app');
    if (appRoot) {
        const app = createApp(App);
        app.use(router);
        app.mount('#app');
    }
    
    // Страница авторизации (отдельная)
    const authRoot = document.getElementById('auth-app');
    if (authRoot) {
        const app = createApp(AuthApp, {
            adminUrl: authRoot.dataset.adminUrl,
        });
        app.mount('#auth-app');
    }
});
