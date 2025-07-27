# Maxmoll

REST API для crm по управлению торговли.

## 📚 Документация API

Документация Swagger доступна по пути:  
`/api/documentation`

## 🚀 Возможности API

### 📦 Склады

-   Просмотр списка складов  
    `GET /api/v1/warehouses`
-   Просмотр конкретного склада  
    `GET /api/v1/warehouses/{id}`

### 🛍️ Товары

-   Просмотр ассортимента с остатками по складам  
    `GET /api/v1/products`
-   Просмотр конкретного товара  
    `GET /api/v1/products/{id}`

### 📊 История движений товаров

-   Просмотр истории с фильтрацией и пагинацией  
    `GET /api/v1/stock-movements`
-   Просмотр конкретной записи  
    `GET /api/v1/stock-movements/{id}`

### 🛒 Управление заказами

-   **Создание заказа** (с несколькими позициями)  
    `POST /api/v1/orders`
-   **Просмотр заказов** (с фильтрами и настраиваемой пагинацией)  
    `GET /api/v1/orders`
-   **Обновление заказа** (данные покупателя и позиции, без изменения статуса)  
    `PUT /api/v1/orders/{id}`
-   **Завершение заказа**  
    `GET /api/v1/orders/{id}/complete`
-   **Отмена заказа**  
    `GET /api/v1/orders/{id}/cancel`
-   **Возобновление заказа** (из статуса "Отменен" в "В работе")  
    `GET /api/v1/orders/{id}/return`

> 🔄 Реализован полный учет движения товаров и остатков на складах

## 🧪 Тестирование

Проект включает комплексные тесты для:

-   Моделей (валидация, отношения)
-   API endpoints (CRUD операции)
-   Бизнес-логики:
    -   Создание заказа с корректным списанием остатков
    -   Изменение статусов заказа
    -   Восстановление отмененных заказов
    -   Контроль остатков товаров

Запуск тестов:

```bash
php artisan test
```

## Технологии

-   **Backend**: Laravel 12
-   **База данных**: MySQL
-   **Документация**: Swagger
-   **Тесты**: PHPUnit

## Установка

1. Клонировать репозиторий:

```bash
git clone https://github.com/ManasArs13/Maxmoll.git && cd Maxmoll
```

2. Установите зависимости:

```bash
composer install && npm install && npm run build
```

3. Настройте:

```bash
cp .env.example .env
php artisan key:generate
```

4. Запустить миграции:

Для удобства тестирования созданы тестовые данные, использую Factories и Seeders

```bash
php artisan migrate --seed
```

## Структура базы данных

-   products
-   orders
-   order_items
-   warehouses
-   stocks
-   stock_movements
