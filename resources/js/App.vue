<template>
    <div id="app" class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-green-50">
        <router-view />
        <ToastContainer />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useCart } from './composables/useCart.js';
import { useFavorites } from './composables/useFavorites.js';
import { useTheme } from './composables/useTheme.js';
import ToastContainer from './components/ToastContainer.vue';

// Загружаем глобальные данные один раз
const { loadCart } = useCart();
const { loadFavorites } = useFavorites();
const { loadTheme } = useTheme();

onMounted(async () => {
    // Загружаем тему первым делом
    await loadTheme();
    
    loadCart();
    
    // Загружаем избранное если пользователь авторизован
    const phone = localStorage.getItem('customer_phone');
    if (phone) {
        loadFavorites();
    }
});
</script>
