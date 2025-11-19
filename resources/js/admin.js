import './bootstrap';
import { createApp } from 'vue';
import router from './router/admin';
import AdminApp from './AdminApp.vue';

const app = createApp(AdminApp);
app.use(router);
app.mount('#admin-app');
