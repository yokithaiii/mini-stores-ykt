# Авторизация клиентов и личный кабинет

## Обзор

Система авторизации клиентов по номеру телефона с кодом подтверждения. Клиенты могут просматривать свои заказы и управлять избранным.

---

## Авторизация

### Процесс входа:

1. **Клиент вводит номер телефона**
   - Формат: +7 (999) 123-45-67
   - Валидация на стороне сервера

2. **Отправка кода**
   - В разработке: всегда код **1111**
   - В продакшене: отправка СМС через API

3. **Ввод кода**
   - 4 цифры
   - Срок действия: 10 минут
   - После успешной проверки - вход в систему

4. **Сохранение сессии**
   - Телефон сохраняется в `localStorage`
   - Данные клиента в `localStorage`
   - Автоматический вход при следующем визите

---

## API Endpoints

### Авторизация

```http
POST /api/customer/send-code
Content-Type: application/json

{
  "phone": "+79991234567"
}

Response:
{
  "message": "Код отправлен на номер +79991234567",
  "dev_code": "1111"
}
```

```http
POST /api/customer/verify-code
Content-Type: application/json

{
  "phone": "+79991234567",
  "code": "1111"
}

Response:
{
  "message": "Успешный вход",
  "token": "...",
  "customer": {
    "id": 1,
    "phone": "+79991234567",
    "name": "Иван",
    "email": "ivan@example.com"
  }
}
```

### Профиль

```http
GET /api/customer/profile
Headers:
  X-Customer-Phone: +79991234567

Response:
{
  "id": 1,
  "phone": "+79991234567",
  "name": "Иван",
  "email": "ivan@example.com"
}
```

```http
POST /api/customer/profile
Headers:
  X-Customer-Phone: +79991234567
Content-Type: application/json

{
  "name": "Иван Иванов",
  "email": "ivan@example.com"
}

Response:
{
  "message": "Профиль обновлен",
  "customer": { ... }
}
```

### Заказы

```http
GET /api/orders
Headers:
  X-Customer-Phone: +79991234567

Response:
[
  {
    "id": 1,
    "customer_id": 1,
    "total_price": 5000,
    "status": "pending",
    "created_at": "2025-11-18T14:00:00",
    "items": [
      {
        "product": { ... },
        "size": "M",
        "quantity": 2,
        "price": 2500
      }
    ]
  }
]
```

### Избранное

```http
GET /api/favorites
Headers:
  X-Customer-Phone: +79991234567

Response:
[
  {
    "id": "uuid",
    "name": "Футболка",
    "price": 1000,
    "image": "/storage/...",
    ...
  }
]
```

```http
POST /api/favorites
Headers:
  X-Customer-Phone: +79991234567
Content-Type: application/json

{
  "product_id": "uuid"
}

Response:
{
  "message": "Товар добавлен в избранное",
  "favorite": { ... }
}
```

```http
DELETE /api/favorites/{productId}
Headers:
  X-Customer-Phone: +79991234567

Response:
{
  "message": "Товар удален из избранного"
}
```

---

## База данных

### Таблица `customers`

```sql
CREATE TABLE customers (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  phone VARCHAR(255) UNIQUE NOT NULL,
  name VARCHAR(255) NULL,
  email VARCHAR(255) NULL,
  verification_code VARCHAR(255) NULL,
  code_expires_at TIMESTAMP NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Таблица `favorites`

```sql
CREATE TABLE favorites (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  customer_id BIGINT NOT NULL,
  product_id UUID NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE (customer_id, product_id)
);
```

### Обновление таблицы `orders`

```sql
ALTER TABLE orders 
ADD COLUMN customer_id BIGINT NULL AFTER id,
ADD FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;
```

---

## Фронтенд

### Компоненты

1. **CustomerAuthModal.vue** - Модальное окно авторизации
   - Ввод телефона
   - Ввод кода
   - Обработка ошибок

2. **CustomerProfileApp.vue** - Личный кабинет
   - Редактирование профиля
   - Список заказов
   - Избранное

3. **ShopApp.vue** - Интеграция
   - Кнопка "Войти" / "Профиль"
   - Кнопка "Избранное"
   - Проверка авторизации

### Роуты

- `/` - Главная (магазин)
- `/profile` - Личный кабинет (требует авторизации)

### LocalStorage

```javascript
// Сохранение после входа
localStorage.setItem('customer_phone', '+79991234567');
localStorage.setItem('customer_data', JSON.stringify(customer));

// Проверка авторизации
const phone = localStorage.getItem('customer_phone');
const isLoggedIn = !!phone;

// Выход
localStorage.removeItem('customer_phone');
localStorage.removeItem('customer_data');
```

---

## Использование

### Для клиента:

1. Открыть главную страницу
2. Нажать "Войти"
3. Ввести номер телефона
4. Ввести код **1111**
5. Перейти в профиль
6. Просмотреть заказы и избранное

### Для разработчика:

```bash
# Запустить миграции
php artisan migrate

# Проверить таблицы
php artisan tinker
>>> Customer::count()
>>> Favorite::count()

# Создать тестового клиента
>>> Customer::create(['phone' => '+79991234567', 'name' => 'Тест'])
```

---

## Безопасность

### Текущая реализация (разработка):

- ✅ Код всегда 1111
- ✅ Срок действия кода: 10 минут
- ✅ Хеширование кода в БД
- ✅ Валидация номера телефона

### Для продакшена:

- ⚠️ Интегрировать SMS API (Twilio, SMS.ru, и т.д.)
- ⚠️ Генерировать случайный 4-значный код
- ⚠️ Ограничить количество попыток
- ⚠️ Добавить rate limiting
- ⚠️ Использовать HTTPS
- ⚠️ Добавить CSRF защиту

---

## Интеграция SMS (для продакшена)

### Пример с SMS.ru:

```php
// app/Services/SmsService.php
class SmsService
{
    public function send($phone, $message)
    {
        $apiId = config('services.smsru.api_id');
        
        $url = "https://sms.ru/sms/send";
        $params = [
            'api_id' => $apiId,
            'to' => $phone,
            'msg' => $message,
            'json' => 1,
        ];
        
        $response = Http::get($url, $params);
        
        return $response->json();
    }
}

// В CustomerAuthController:
$code = rand(1000, 9999);
app(SmsService::class)->send($phone, "Ваш код: $code");
```

---

## Тестирование

```bash
# Тест авторизации
curl -X POST http://localhost/api/customer/send-code \
  -H "Content-Type: application/json" \
  -d '{"phone": "+79991234567"}'

curl -X POST http://localhost/api/customer/verify-code \
  -H "Content-Type: application/json" \
  -d '{"phone": "+79991234567", "code": "1111"}'

# Тест профиля
curl http://localhost/api/customer/profile \
  -H "X-Customer-Phone: +79991234567"

# Тест избранного
curl http://localhost/api/favorites \
  -H "X-Customer-Phone: +79991234567"
```

---

## Troubleshooting

### Проблема: Код не принимается

**Решение:** Проверьте, что код не истек (10 минут)

### Проблема: Не видно заказов

**Решение:** Убедитесь, что заказы создавались после авторизации (с заголовком X-Customer-Phone)

### Проблема: Избранное не сохраняется

**Решение:** Проверьте авторизацию и наличие заголовка X-Customer-Phone в запросах

---

**Последнее обновление:** 18 ноября 2025
