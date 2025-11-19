import { ref, watch } from 'vue';
import axios from 'axios';
import { themeConfig as defaultTheme } from '../config/theme.js';

// Глобальное состояние темы
const theme = ref({ ...defaultTheme });
const isLoaded = ref(false);

export function useTheme() {
    const loadTheme = async () => {
        if (isLoaded.value) return;
        
        try {
            const response = await axios.get('/api/theme');
            const customTheme = response.data;
            
            // Применяем кастомные цвета, если они есть
            if (customTheme.primary_color) {
                theme.value.primary[500] = customTheme.primary_color;
            }
            if (customTheme.accent_color) {
                theme.value.accent[500] = customTheme.accent_color;
            }
            if (customTheme.success_color) {
                theme.value.success = customTheme.success_color;
            }
            if (customTheme.warning_color) {
                theme.value.warning = customTheme.warning_color;
            }
            if (customTheme.error_color) {
                theme.value.error = customTheme.error_color;
            }
            
            isLoaded.value = true;
            
            // Применяем CSS переменные для использования в стилях
            applyThemeVariables();
        } catch (error) {
            console.error('Ошибка загрузки темы:', error);
            // Используем дефолтную тему при ошибке
            isLoaded.value = true;
        }
    };

    const applyThemeVariables = () => {
        const root = document.documentElement;
        root.style.setProperty('--color-primary', theme.value.primary[500]);
        root.style.setProperty('--color-accent', theme.value.accent[500]);
        root.style.setProperty('--color-success', theme.value.success);
        root.style.setProperty('--color-warning', theme.value.warning);
        root.style.setProperty('--color-error', theme.value.error);
    };

    const updateTheme = (newTheme) => {
        if (newTheme.primary_color) {
            theme.value.primary[500] = newTheme.primary_color;
        }
        if (newTheme.accent_color) {
            theme.value.accent[500] = newTheme.accent_color;
        }
        if (newTheme.success_color) {
            theme.value.success = newTheme.success_color;
        }
        if (newTheme.warning_color) {
            theme.value.warning = newTheme.warning_color;
        }
        if (newTheme.error_color) {
            theme.value.error = newTheme.error_color;
        }
        applyThemeVariables();
    };

    const resetTheme = () => {
        theme.value = { ...defaultTheme };
        applyThemeVariables();
    };

    return {
        theme,
        loadTheme,
        updateTheme,
        resetTheme,
        isLoaded,
    };
}
