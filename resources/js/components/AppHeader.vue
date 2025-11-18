<template>
    <header class="sticky top-0 z-40 bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-4">
            <div class="flex items-center justify-between">
                <router-link to="/" class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900">Mini Stores</h1>
                    <p class="text-sm text-gray-600 hidden sm:block">Интернет-магазин</p>
                </router-link>
                
                <div class="flex items-center gap-3">
                    <!-- Избранное (только для авторизованных) -->
                    <router-link
                        v-if="isCustomerLoggedIn"
                        to="/profile"
                        class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-gray-300 text-gray-700 transition hover:bg-gray-50"
                        title="Избранное"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span
                            v-if="favoritesCount > 0"
                            class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full text-xs font-bold text-white"
                            :style="{ backgroundColor: colors.error }"
                        >
                            {{ favoritesCount }}
                        </span>
                    </router-link>
                    
                    <!-- Корзина -->
                    <button
                        class="relative flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-sm transition-all hover:shadow-md"
                        :style="{ backgroundColor: colors.primary[500] }"
                        @click="$emit('toggle-cart')"
                        title="Корзина"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span
                            v-if="cartItemsCount > 0"
                            class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full text-xs font-bold text-white"
                            :style="{ backgroundColor: colors.accent[500] }"
                        >
                            {{ cartItemsCount }}
                        </span>
                    </button>
                    
                    <!-- Профиль или Вход -->
                    <button
                        v-if="isCustomerLoggedIn"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-300 text-gray-700 transition hover:bg-gray-50"
                        @click="goToProfile"
                        :title="customerName || 'Профиль'"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </button>
                    <button
                        v-else
                        class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="$emit('show-auth')"
                    >
                        Войти
                    </button>
                    
                    <!-- Админка -->
                    <a
                        href="/auth"
                        class="hidden sm:flex h-10 w-10 items-center justify-center rounded-xl border border-gray-300 text-gray-700 transition hover:bg-gray-50"
                        title="Админка"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { themeConfig } from '../config/theme.js';

const colors = themeConfig;

defineProps({
    isCustomerLoggedIn: Boolean,
    customerName: String,
    cartItemsCount: Number,
    favoritesCount: Number,
});

defineEmits(['toggle-cart', 'show-auth']);

import { useRouter } from 'vue-router';

const router = useRouter();

const goToProfile = () => {
    router.push('/profile');
};
</script>
