<?php

namespace App\Listeners;

use App\Events\IncrementStockEvent;
use App\Models\Stock;
use App\Models\StockMovement;

class IncrementStockListener
{
    /**
     * Обработчик увеличения остатков товаров
     *
     * @param IncrementStockEvent $event Событие изменения остатков
     * @return void
     * @throws \Throwable
     */
    public function handle(IncrementStockEvent $event): void
    {
        $products = $event->order->products()->get();

        foreach ($products as $item) {

            // Уменьшаем остаток
            Stock::where([
                'warehouse_id' => $event->order->warehouse_id,
                'product_id' => $item->id,
            ])->increment('stock', $item->pivot->count);

            // Получаем обновленный остаток
            $stock = Stock::where([
                'warehouse_id' => $event->order->warehouse_id,
                'product_id' => $item->id,
            ])->First();

            // Регистрируем движение
            StockMovement::create([
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'quantity' => $item->pivot->count,
                'balance_after' => $stock->stock,
            ]);
        }
    }
}
