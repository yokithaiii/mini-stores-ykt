# ✅ Чеклист перед деплоем

## 1. Код
- [x] Все изменения закоммичены
- [x] `npm run build` выполнен успешно
- [x] Нет ошибок в консоли
- [ ] Все TODO выполнены или удалены

## 2. Конфигурация
- [ ] `.env.example` обновлен
- [ ] `APP_DEBUG=false` для продакшена
- [ ] `APP_ENV=production`
- [ ] Настроены переменные БД

## 3. База данных
- [x] Все миграции созданы
- [x] Миграции тестированы локально
- [ ] Есть сиды для тестовых данных (опционально)

## 4. Безопасность
- [ ] Сменить дефолтные пароли
- [ ] Проверить CORS настройки
- [ ] Настроить rate limiting
- [ ] Проверить валидацию форм

## 5. Производительность
- [x] `composer install --optimize-autoloader`
- [x] `php artisan config:cache`
- [x] `php artisan route:cache`
- [x] `php artisan view:cache`
- [x] Assets собраны (`npm run build`)

## 6. Файлы для деплоя
- [x] `nixpacks.toml` создан
- [x] `railway.json` создан
- [x] `Procfile` создан
- [x] `deploy.sh` создан
- [x] `create-admin.php` создан

## 7. Тестирование
- [ ] Локально всё работает
- [ ] Авторизация работает
- [ ] CRUD операции работают
- [ ] Загрузка файлов работает
- [ ] Корзина работает
- [ ] Оформление заказа работает

## 8. Документация
- [x] `DEPLOYMENT.md` создан
- [x] `QUICK-DEPLOY.md` создан
- [x] `README.md` обновлен (если нужно)

## 9. Git
```bash
# Проверь статус
git status

# Закоммить всё
git add .
git commit -m "Ready for production deployment"

# Запушить
git push origin main
```

## 10. После деплоя
- [ ] Запустить миграции
- [ ] Создать админа
- [ ] Проверить все страницы
- [ ] Проверить мобильную версию
- [ ] Настроить домен (опционально)
- [ ] Настроить мониторинг (опционально)

---

## Быстрая команда для подготовки

```bash
# Всё в одной команде
php artisan config:clear && \
php artisan cache:clear && \
composer install --no-dev --optimize-autoloader && \
npm ci && \
npm run build && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
echo "✅ Готово к деплою!"
```

---

## Если что-то пошло не так

### Откатить деплой
```bash
# Railway
railway rollback

# Heroku
heroku rollback

# Render
Через Dashboard → Rollback
```

### Посмотреть логи
```bash
# Railway
railway logs

# Heroku
heroku logs --tail

# Render
Dashboard → Logs
```

### Перезапустить
```bash
# Railway
railway restart

# Heroku
heroku restart

# Render
Dashboard → Manual Deploy
```

---

## 🚀 Готов к деплою!

Следуй инструкциям в `QUICK-DEPLOY.md`
