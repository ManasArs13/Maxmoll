<?php

namespace App\Actions\Api\V1\Orders;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    /**
     * Применяет отмену заказа:
     * - меняет статус заказа на "canceled"
     * - возвращает товары на склад
     * - записывает движение товаров
     *
     * @param Order $order Заказ для отмены
     * @return Order Обновленный заказ с подгруженными отношениями
     * @throws \Throwable Если произошла ошибка при выполнении транзакции
     */
    public function apply(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'canceled'
            ]);

            $this->incrementStock($order);

            return $order->load(['warehouse', 'products']);
        });
    }

    /**
     * Увеличивает остатки товаров на складе и регистрирует движение товаров
     *
     * @param Order $order Заказ, товары которого нужно вернуть на склад
     * @return void
     */
    protected function incrementStock($order): void
    {
        $products = $order->products()->get();

        foreach ($products as $item) {
            Stock::where([
                'warehouse_id' => $order->warehouse_id,
                'product_id' => $item->id,
            ])->increment('stock', $item->pivot->count);

            $stock = Stock::where([
                'warehouse_id' => $order->warehouse_id,
                'product_id' => $item->id,
            ])->First();

            StockMovement::create([
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'quantity' => $item->pivot->count,
                'balance_after' => $stock->stock,
            ]);
        }
    }
}
