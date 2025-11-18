<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-green-50">
        <!-- Шапка -->
        <AppHeader
            :is-customer-logged-in="isCustomerLoggedIn"
            :customer-name="customerName"
            :cart-items-count="cartItemsCount"
            :favorites-count="favoritesCount"
            @toggle-cart="showCart = true"
            @show-auth="showAuthModal = true"
        />

        <main class="mx-auto max-w-7xl px-4 py-8">
            <!-- Скелетон загрузки -->
            <div v-if="loading" class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5">
                <div class="grid gap-8 lg:grid-cols-2">
                    <!-- Скелетон изображения -->
                    <div class="space-y-4">
                        <div class="aspect-square animate-pulse rounded-xl bg-gray-200"></div>
                        <div class="flex gap-2">
                            <div class="h-20 w-20 animate-pulse rounded-lg bg-gray-200"></div>
                            <div class="h-20 w-20 animate-pulse rounded-lg bg-gray-200"></div>
                            <div class="h-20 w-20 animate-pulse rounded-lg bg-gray-200"></div>
                        </div>
                    </div>

                    <!-- Скелетон информации -->
                    <div class="space-y-6">
                        <div>
                            <div class="h-8 w-3/4 animate-pulse rounded bg-gray-200"></div>
                            <div class="mt-2 h-4 w-1/2 animate-pulse rounded bg-gray-200"></div>
                        </div>
                        <div>
                            <div class="h-10 w-1/3 animate-pulse rounded bg-gray-200"></div>
                            <div class="mt-2 h-4 w-1/4 animate-pulse rounded bg-gray-200"></div>
                        </div>
                        <div class="border-t border-gray-200 pt-6">
                            <div class="h-6 w-1/4 animate-pulse rounded bg-gray-200 mb-3"></div>
                            <div class="flex gap-2">
                                <div class="h-12 w-16 animate-pulse rounded-lg bg-gray-200"></div>
                                <div class="h-12 w-16 animate-pulse rounded-lg bg-gray-200"></div>
                                <div class="h-12 w-16 animate-pulse rounded-lg bg-gray-200"></div>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-6">
                            <div class="h-14 w-full animate-pulse rounded-xl bg-gray-200"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Товар не найден -->
            <div v-else-if="!product" class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-900/5">
                <p class="text-gray-500">Товар не найден</p>
                <a href="/" class="mt-4 inline-block text-blue-600 hover:text-blue-700">
                    Вернуться в каталог
                </a>
            </div>

            <!-- Детали товара -->
            <div v-else class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5">
                <div class="grid gap-8 lg:grid-cols-2">
                    <!-- Галерея изображений -->
                    <div class="space-y-4">
                        <!-- Главное изображение -->
                        <div class="aspect-square overflow-hidden rounded-xl bg-gray-100">
                            <img 
                                :src="getCurrentImage()" 
                                :alt="product.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        
                        <!-- Thumbnails -->
                        <div v-if="getAllImages().length > 1" class="flex gap-2 overflow-x-auto">
                            <button
                                v-for="(img, index) in getAllImages()"
                                :key="index"
                                class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg border-2 transition"
                                :class="currentImageIndex === index ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-200 hover:border-gray-300'"
                                @click="currentImageIndex = index"
                            >
                                <img :src="img" :alt="`${product.name} ${index + 1}`" class="h-full w-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <!-- Информация -->
                    <div class="space-y-6 flex flex-col">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ product.name }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-600">
                                <span v-if="product.brand">{{ product.brand.name }}</span>
                                <span v-if="product.brand && categoryName">•</span>
                                <span>{{ categoryName }}</span>
                                <span v-if="product.gender">•</span>
                                <span v-if="product.gender">{{ getGenderLabel(product.gender) }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center gap-3">
                                <p 
                                    v-if="product.discount_type !== 'none' && product.discount_value"
                                    class="text-2xl text-gray-400 line-through"
                                >
                                    {{ formatPrice(product.price) }}
                                </p>
                                <p class="text-4xl font-bold" :style="{ color: colors.accent[600] }">
                                    {{ formatPrice(product.discounted_price || product.price) }}
                                </p>
                            </div>
                            <div v-if="product.discount_type !== 'none' && product.discount_value" class="mt-2">
                                <span class="inline-block rounded-full px-4 py-1 text-sm font-bold text-white" :style="{ backgroundColor: colors.error }">
                                    Скидка {{ formatDiscount() }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-gray-600">
                                В наличии: {{ getTotalQuantity() }} шт.
                            </p>
                        </div>

                        <!-- Размеры - ОБЯЗАТЕЛЬНО -->
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Выберите размер <span class="text-red-500">*</span>
                            </h2>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button
                                    v-for="variant in product.variants"
                                    :key="variant.id"
                                    class="rounded-lg border px-5 py-3 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="selectedVariant?.id === variant.id ? 'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500/20' : 'border-gray-300 text-gray-700 hover:border-gray-400'"
                                    :disabled="variant.quantity === 0"
                                    @click="selectVariant(variant)"
                                >
                                    {{ variant.size }}
                                    <span v-if="variant.quantity > 0" class="ml-1 text-xs text-gray-500">({{ variant.quantity }})</span>
                                    <span v-else class="ml-1 text-xs text-red-500">(нет)</span>
                                </button>
                            </div>
                            <p v-if="!selectedVariant" class="mt-2 text-sm text-red-600">
                                Выберите размер для добавления в корзину
                            </p>
                        </div>

                        <!-- Характеристики -->
                        <div v-if="product.attributes && product.attributes.length > 0" class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold text-gray-900">Характеристики</h2>
                            <dl class="mt-3 space-y-2">
                                <div v-for="(attr, index) in product.attributes" :key="index" class="flex justify-between text-sm">
                                    <dt class="text-gray-600">{{ attr.key }}:</dt>
                                    <dd class="font-medium text-gray-900">{{ attr.value }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div v-if="product.description" class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold text-gray-900">Описание</h2>
                            <p class="mt-3 whitespace-pre-line text-gray-700">{{ product.description }}</p>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="border-t border-gray-200 pt-6 mt-auto">
                            <!-- Если товар НЕ в корзине -->
                            <button
                                v-if="!isInCart()"
                                class="w-full rounded-xl py-4 text-lg font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                                :style="{ backgroundColor: canAddToCart() ? colors.primary[500] : colors.neutral[400] }"
                                :disabled="!canAddToCart()"
                                @click="addToCart"
                            >
                                {{ getAddToCartButtonText() }}
                            </button>
                            
                            <!-- Если товар В корзине - показываем кнопки +/- -->
                            <div v-else class="flex items-center justify-center gap-4">
                                <button
                                    class="flex h-12 w-12 items-center justify-center rounded-lg border-2 border-gray-300 text-2xl font-bold text-gray-700 transition hover:bg-gray-50"
                                    @click="decreaseQuantityInCart"
                                >
                                    −
                                </button>
                                <span class="min-w-[4rem] text-center text-2xl font-bold text-gray-900">
                                    {{ getCartQuantity() }}
                                </span>
                                <button
                                    class="flex h-12 w-12 items-center justify-center rounded-lg border-2 transition disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="canIncreaseInCart() ? 'border-blue-500 text-blue-600 hover:bg-blue-50' : 'border-gray-300 text-gray-400'"
                                    :disabled="!canIncreaseInCart()"
                                    @click="increaseQuantityInCart"
                                >
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Корзина -->
        <CartSidebar
            :show="showCart"
            @close="showCart = false"
            @checkout="openCheckout"
        />

        <!-- Модалка оформления заказа -->
        <CheckoutModal
            :show="showCheckoutModal"
            :items="cart"
            :total-price="totalPrice"
            :is-logged-in="isCustomerLoggedIn"
            :customer-name="customerName"
            :customer-phone="customerPhone"
            @close="showCheckoutModal = false"
            @success="onOrderSuccess"
            @show-auth="showAuthModal = true; showCheckoutModal = false"
        />

        <!-- Модалка авторизации -->
        <CustomerAuthModal
            :show="showAuthModal"
            @close="showAuthModal = false"
            @success="onAuthSuccess"
        />
    </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { themeConfig } from '../config/theme.js';
import { useCart } from '../composables/useCart.js';
import { useFavorites } from '../composables/useFavorites.js';
import CartSidebar from './CartSidebar.vue';
import AppHeader from './AppHeader.vue';
import CustomerAuthModal from './CustomerAuthModal.vue';
import CheckoutModal from './CheckoutModal.vue';

const route = useRoute();
const router = useRouter();
const { cart, cartItemsCount, saveCart, clearCart } = useCart();
const { favoritesCount } = useFavorites();

const colors = themeConfig;

const product = ref(null);
const loading = ref(true);
const selectedVariant = ref(null);
const currentImageIndex = ref(0);
const categories = ref([]);
const showCart = ref(false);
const showCheckoutModal = ref(false);
const showAuthModal = ref(false);
const isCustomerLoggedIn = ref(false);
const customerName = ref('');
const customerPhone = ref('');

const productId = computed(() => route.params.id);

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data.data || response.data;
    } catch (error) {
        console.error(error);
    }
};

const fetchProduct = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/products/${productId.value}`);
        product.value = response.data.data || response.data;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};



const getAllImages = () => {
    if (!product.value) return [];
    const images = [];
    if (product.value.image) images.push(product.value.image);
    if (product.value.images && Array.isArray(product.value.images)) {
        images.push(...product.value.images);
    }
    return images;
};

const getCurrentImage = () => {
    const images = getAllImages();
    return images[currentImageIndex.value] || '/placeholder.jpg';
};

const getGenderLabel = (gender) => {
    const labels = {
        male: 'Мужской',
        female: 'Женский',
        unisex: 'Унисекс',
    };
    return labels[gender] || '';
};

const formatPrice = (value) => {
    try {
        return new Intl.NumberFormat('ru-RU', {
            style: 'currency',
            currency: 'RUB',
            maximumFractionDigits: 0,
        }).format(value ?? 0);
    } catch (error) {
        return `${value} ₽`;
    }
};

const formatDiscount = () => {
    if (!product.value || product.value.discount_type === 'none' || !product.value.discount_value) {
        return '';
    }

    if (product.value.discount_type === 'percent') {
        return `${product.value.discount_value}%`;
    }

    return formatPrice(product.value.discount_value);
};

const getTotalQuantity = () => {
    if (!product.value) return 0;
    
    if (product.value.variants && product.value.variants.length > 0) {
        return product.value.variants.reduce((sum, v) => sum + v.quantity, 0);
    }
    
    return 0;
};

const selectVariant = (variant) => {
    selectedVariant.value = variant;
};

const canAddToCart = () => {
    if (!product.value) return false;
    return selectedVariant.value && selectedVariant.value.quantity > 0;
};

const getAddToCartButtonText = () => {
    if (!product.value) return 'Недоступно';
    
    if (!selectedVariant.value) {
        return 'Выберите размер';
    }
    
    if (selectedVariant.value.quantity === 0) {
        return 'Нет в наличии';
    }
    
    return `Добавить в корзину`;
};

const addToCart = () => {
    if (!canAddToCart()) return;
    
    const cartItem = {
        ...product.value,
        selectedSize: selectedVariant.value?.size || null,
        selectedVariantId: selectedVariant.value?.id || null,
        cartQuantity: 1,
    };
    
    const existingIndex = cart.value.findIndex(item => 
        item.id === product.value.id && item.selectedSize === cartItem.selectedSize
    );
    
    if (existingIndex >= 0) {
        cart.value[existingIndex].cartQuantity++;
    } else {
        cart.value.push(cartItem);
    }
    
    saveCart();
    updateCartCount();
    
    // Открываем корзину на этой же странице
    showCart.value = true;
};

const goToCart = () => {
    showCart.value = true;
};

const updateCartCount = () => {
    // Ничего не делаем, счетчик обновляется автоматически через computed
};

const isInCart = () => {
    if (!product.value || !selectedVariant.value) return false;
    return cart.value.some(item => 
        item.id === product.value.id && item.selectedSize === selectedVariant.value.size
    );
};

const getCartQuantity = () => {
    if (!product.value || !selectedVariant.value) return 0;
    const item = cart.value.find(item => 
        item.id === product.value.id && item.selectedSize === selectedVariant.value.size
    );
    return item ? item.cartQuantity : 0;
};

const canIncreaseInCart = () => {
    if (!product.value || !selectedVariant.value) return false;
    const item = cart.value.find(item => 
        item.id === product.value.id && item.selectedSize === selectedVariant.value.size
    );
    if (!item) return false;
    return item.cartQuantity < selectedVariant.value.quantity;
};

const increaseQuantityInCart = () => {
    if (!product.value || !selectedVariant.value) return;
    const item = cart.value.find(item => 
        item.id === product.value.id && item.selectedSize === selectedVariant.value.size
    );
    if (item && item.cartQuantity < selectedVariant.value.quantity) {
        item.cartQuantity++;
        saveCart();
        updateCartCount();
    }
};

const decreaseQuantityInCart = () => {
    if (!product.value || !selectedVariant.value) return;
    const index = cart.value.findIndex(item => 
        item.id === product.value.id && item.selectedSize === selectedVariant.value.size
    );
    if (index >= 0) {
        if (cart.value[index].cartQuantity > 1) {
            cart.value[index].cartQuantity--;
        } else {
            cart.value.splice(index, 1);
        }
        saveCart();
        updateCartCount();
    }
};

const checkCustomerAuth = () => {
    const phone = localStorage.getItem('customer_phone');
    const customerData = localStorage.getItem('customer_data');
    
    if (phone && customerData) {
        isCustomerLoggedIn.value = true;
        customerPhone.value = phone;
        try {
            const data = JSON.parse(customerData);
            customerName.value = data.name || phone;
        } catch (e) {
            customerName.value = phone;
        }
    }
};

const onAuthSuccess = (customer) => {
    isCustomerLoggedIn.value = true;
    customerName.value = customer.name || customer.phone;
    customerPhone.value = customer.phone;
};

const openCheckout = () => {
    showCart.value = false;
    showCheckoutModal.value = true;
};

const onOrderSuccess = () => {
    clearCart();
    showCheckoutModal.value = false;
    alert('Заказ успешно оформлен! Мы свяжемся с вами в ближайшее время.');
    router.push('/profile');
};

const totalPrice = computed(() => {
    return cart.value.reduce((sum, item) => {
        const price = item.discounted_price || item.price;
        return sum + (price * item.cartQuantity);
    }, 0);
});

const categoryName = computed(() => {
    if (product.value?.category?.name) {
        return product.value.category.name;
    }
    const category = categories.value.find(c => c.id === product.value?.category_id);
    return category?.name || '';
});

onMounted(async () => {
    checkCustomerAuth();
    await Promise.all([fetchCategories(), fetchProduct()]);
});
</script>
