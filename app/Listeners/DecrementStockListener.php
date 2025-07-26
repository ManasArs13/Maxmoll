<?php

namespace App\Listeners;

use App\Events\DecrementStockEvent;
use App\Models\Stock;
use App\Models\StockMovement;

class DecrementStockListener
{
    /**
     * Обработчик события уменьшения остатков товаров
     *
     * @param DecrementStockEvent $event Событие изменения остатков
     * @return void
     * @throws \Throwable
     */
    public function handle(DecrementStockEvent $event): void
    {
        $products = $event->order->products()->get();

        foreach ($products as $item) {

            // Уменьшаем остаток
            Stock::where([
                'warehouse_id' => $event->order->warehouse_id,
                'product_id' => $item->id,
            ])->decrement('stock', $item->pivot->count);

            // Получаем обновленный остаток
            $stock = Stock::where([
                'warehouse_id' => $event->order->warehouse_id,
                'product_id' => $item->id,
            ])->firstOrFail();

            // Регистрируем движение
            StockMovement::create([
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'quantity' => -$item->pivot->count,
                'balance_after' => $stock->stock,
            ]);
        }
    }
}
