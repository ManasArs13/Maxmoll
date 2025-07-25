<?php

namespace App\Actions\Api\V1;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReturnOrderAction
{
    /**
     * Обрабатывает возврат заказа:
     * - Возвращает заказ в статус "active"
     * - Уменьшает остатки товаров на складе (компенсируя предыдущее увеличение)
     * - Фиксирует движение товаров
     * 
     * Все операции выполняются в транзакции для атомарности
     *
     * @param Order $order Заказ для возврата
     * @return Order Обновленный заказ с подгруженными связями
     * @throws \Throwable При ошибке выполнения транзакции
     */
    public function apply(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'active'
            ]);

            $this->decrementStock($order);

            return $order->load(['warehouse', 'products']);
        });
    }

    /**
     * Уменьшает остатки товаров на складе и регистрирует движение товаров
     * 
     * Используется при возврате заказа для компенсации ранее увеличенных остатков
     *
     * @param Order $order Заказ с товарами для возврата
     * @return void
     */
    protected function decrementStock($order): void
    {
        $products = $order->products()->get();

        foreach ($products as $item) {
            Stock::where([
                'warehouse_id' => $order->warehouse_id,
                'product_id' => $item->id,
            ])->decrement('stock', $item->pivot->count);

            $stock = Stock::where([
                'warehouse_id' => $order->warehouse_id,
                'product_id' => $item->id,
            ])->First();

            StockMovement::create([
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'quantity' => -$item->pivot->count,
                'balance_after' => $stock->stock,
            ]);
        }
    }
}
