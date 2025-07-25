<?php

namespace App\Actions\Api\V1;

use App\Models\Order;

class CompleteOrderAction
{
    /**
     * Помечает заказ как выполненный:
     * - Обновляет статус заказа на "completed"
     * - Возвращает обновленный заказ с подгруженными связанными данными
     *
     * @param Order $order Заказ, который нужно завершить
     * @return Order Обновленный заказ с подгруженными отношениями warehouse и products
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если заказ не найден
     * @throws \Illuminate\Database\QueryException При ошибке работы с базой данных
     */
    public function apply(Order $order): Order
    {
        $order->update([
            'status' => 'completed'
        ]);

        return $order->load(['warehouse', 'products']);
    }
}
