<?php

namespace App\Actions\Api\V1;

use App\Events\DecrementStockEvent;
use App\Models\Order;
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

            // Событие для уменьшения остатки на складе
            event(new DecrementStockEvent($order));

            return $order->load(['warehouse', 'products']);
        });
    }
}
