<?php

namespace App\Actions\Api\V1;

use App\Events\DecrementStockEvent;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    /**
     * Создает новый заказ и выполняет связанные операции:
     * - Создает запись заказа
     * - Добавляет товары в заказ
     * - Уменьшает остатки на складе
     * - Фиксирует движение товаров
     *
     * Все операции выполняются в транзакции для обеспечения целостности данных
     *
     * @param array $orderData Массив с данными для создания заказа:
     *               - customer: Имя клиента
     *               - warehouse_id: ID склада
     *               - products: Массив товаров [['product_id' => X, 'count' => Y]]
     * @return Order Созданный заказ с подгруженными отношениями
     * @throws \Throwable Если произошла ошибка при выполнении транзакции
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если товар не найден
     */
    public function apply(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {

            $order = $this->createOrder($orderData);

            // Событие для уменьшения остатки на складе
            event(new DecrementStockEvent($order, $orderData));

            return $order->load(['warehouse', 'products']);
        });
    }

    /**
     * Создает основную запись заказа в базе данных
     * Добавляет товары в заказ через промежуточную таблицу
     *
     * @param array $orderData Данные для создания заказа
     * @return Order Созданный заказ
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если товар не найден
     */
    protected function createOrder(array $orderData): Order
    {
        $order = Order::create([
            'customer' => $orderData['customer'],
            'warehouse_id' => $orderData['warehouse_id'],
            'created_at' => now(),
            'status' => 'active',
        ]);

        foreach ($orderData['products'] as $item) {
            $order->products()->attach(
                Product::findOrFail($item['product_id']),
                ['count' => $item['count']]
            );
        }

        return $order;
    }
}
