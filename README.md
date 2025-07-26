# Maxmoll

REST API для crm по управлению торговли.

## Доступ к документации Swagger

Документация доступна по пути 'api/documentation'

## Возможности

-   Просмотр ассортимента продуктов и их остатков
    'api/v1/products';
    'api/v1/products/ID;
-   Просмотр заказов (с фильтрами и пагинацией)
    'api/v1/orders';
-   Просмотр складов
    'api/v1/warehouses';
    'api/v1/warehouses/ID;
-   Просмотр истории движения товаров (с фильтрами и пагинацией)
    'api/v1/stock-movements';
    'api/v1/stock-movements/ID;

-   Создать заказ: POST Api/v1/orders
-   Обновить заказ (данные покупателя и список позиций, но не статус): PUT Api/v1/orders/ID
-   Завершить заказ: GET Api/v1/orders/ID/complete
-   Отменить заказ: GET Api/v1/orders/ID/cancel
-   Возобновить заказ (перевод из отмены в работу): GET Api/v1/orders/ID/return

(Реализовано учёт движение товаров и остатков)

## Технологии

-   **Backend**: Laravel 12
-   **База данных**: MySQL
-   **Документация**: Swagger

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
