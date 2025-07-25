<?php

namespace App\Actions\Api\V1\Orders;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReturnOrderAction
{
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
