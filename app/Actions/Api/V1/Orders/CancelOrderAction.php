<?php

namespace App\Actions\Api\V1\Orders;

use App\Models\Order;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
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

    protected function incrementStock($order): void
    {
        $products = $order->products()->get();

        foreach ($products as $item) {
            Stock::where([
                'warehouse_id' => $order->warehouse_id,
                'product_id' => $item->id,
            ])->increment('stock', $item->pivot->count);
        }
    }
}
