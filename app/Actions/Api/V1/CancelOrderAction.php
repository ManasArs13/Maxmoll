<?php

namespace App\Actions\Api\V1;

use App\Events\IncrementStockEvent;
use App\Models\Order;
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

            // Событие для возврата остатков на склад
            event(new IncrementStockEvent($order));

            return $order->load(['warehouse', 'products']);
        });
    }
}
