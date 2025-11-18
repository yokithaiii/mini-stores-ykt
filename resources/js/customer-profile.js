import './bootstrap';
import { createApp } from 'vue';
import CustomerProfileApp from './components/CustomerProfileApp.vue';

const app = createApp(CustomerProfileApp);
app.mount('#app');
