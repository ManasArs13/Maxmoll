<?php

namespace App\Actions\Api\V1\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
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
            $this->addItemsToOrder($order, $orderData['products']);
            $this->decrementStock($orderData);

            return $order->load(['warehouse', 'products']);
        });
    }

    /**
     * Создает основную запись заказа в базе данных
     *
     * @param array $orderData Данные для создания заказа
     * @return Order Созданный заказ
     */
    protected function createOrder(array $orderData): Order
    {
        return Order::create([
            'customer' => $orderData['customer'],
            'warehouse_id' => $orderData['warehouse_id'],
            'created_at' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * Добавляет товары в заказ через промежуточную таблицу
     *
     * @param Order $order Заказ, к которому добавляются товары
     * @param array $items Массив товаров для добавления
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если товар не найден
     */
    protected function addItemsToOrder(Order $order, array $items): void
    {
        foreach ($items as $item) {

            $order->products()->attach(
                Product::findOrFail($item['product_id']),
                ['count' => $item['count']]
            );
        }
    }

    /**
     * Уменьшает остатки товаров на складе и регистрирует движение товаров
     *
     * @param array $orderData Данные заказа, включая warehouse_id и список товаров
     * @return void
     */
    protected function decrementStock(array $orderData): void
    {
        foreach ($orderData['products'] as $item) {
            Stock::where([
                'warehouse_id' => $orderData['warehouse_id'],
                'product_id' => $item['product_id']
            ])->decrement('stock', $item['count']);

            $stock = Stock::where([
                'warehouse_id' => $orderData['warehouse_id'],
                'product_id' => $item['product_id']
            ])->First();

            StockMovement::create([
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'quantity' => -$item['count'],
                'balance_after' => $stock->stock,
            ]);
        }
    }
}
