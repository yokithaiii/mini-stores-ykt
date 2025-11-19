import { ref, computed } from 'vue';
import axios from 'axios';

// Глобальное состояние избранного
const favorites = ref([]);
const isLoaded = ref(false);

export function useFavorites() {
    const loadFavorites = async (force = false) => {
        const phone = localStorage.getItem('customer_phone');
        if (!phone) return;
        
        // Если уже загружено и не принудительная перезагрузка - пропускаем
        if (isLoaded.value && !force) return;
        
        try {
            const response = await axios.get('/api/favorites', {
                headers: {
                    'X-Customer-Phone': phone,
                },
            });
            favorites.value = response.data.map(p => p.id);
            isLoaded.value = true;
        } catch (error) {
            console.error('Ошибка загрузки избранного:', error);
            // Сбрасываем флаг при ошибке, чтобы можно было попробовать снова
            isLoaded.value = false;
        }
    };

    const favoritesCount = computed(() => favorites.value.length);

    const isInFavorites = (productId) => {
        return favorites.value.includes(productId);
    };

    const addToFavorites = async (productId) => {
        const phone = localStorage.getItem('customer_phone');
        if (!phone) return;

        try {
            await axios.post('/api/favorites', 
                { product_id: productId },
                {
                    headers: {
                        'X-Customer-Phone': phone,
                    },
                }
            );
            favorites.value.push(productId);
        } catch (error) {
            console.error('Ошибка добавления в избранное:', error);
        }
    };

    const removeFromFavorites = async (productId) => {
        const phone = localStorage.getItem('customer_phone');
        if (!phone) return;

        try {
            await axios.delete(`/api/favorites/${productId}`, {
                headers: {
                    'X-Customer-Phone': phone,
                },
            });
            favorites.value = favorites.value.filter(id => id !== productId);
        } catch (error) {
            console.error('Ошибка удаления из избранного:', error);
        }
    };

    const clearFavorites = () => {
        favorites.value = [];
        isLoaded.value = false;
    };

    return {
        favorites,
        favoritesCount,
        loadFavorites,
        isInFavorites,
        addToFavorites,
        removeFromFavorites,
        clearFavorites,
    };
}
