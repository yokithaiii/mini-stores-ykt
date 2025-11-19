#!/bin/bash

echo "🚀 Подготовка к деплою..."

# Очистка кэша
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Установка зависимостей
echo "📦 Установка зависимостей..."
composer install --no-dev --optimize-autoloader
npm ci

# Сборка фронтенда
echo "🔨 Сборка фронтенда..."
npm run build

# Оптимизация
echo "⚡ Оптимизация..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Миграции
echo "🗄️ Запуск миграций..."
php artisan migrate --force

# Создание storage link
php artisan storage:link

echo "✅ Готово к деплою!"
