<?php

namespace App\Actions\Api\V1\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function apply(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {

            $order = $this->createOrder($orderData);
            $this->addItemsToOrder($order, $orderData['products']);
            $this->decrementStock($orderData);

            return $order->load(['warehouse', 'products']);
        });
    }

    protected function createOrder(array $orderData): Order
    {
        return Order::create([
            'customer' => $orderData['customer'],
            'warehouse_id' => $orderData['warehouse_id'],
            'created_at' => now(),
            'status' => 'active',
        ]);
    }

    protected function addItemsToOrder(Order $order, array $items): void
    {
        foreach ($items as $item) {
            
            $order->products()->attach(
                Product::findOrFail($item['product_id']),
                ['count' => $item['count']]
            );
        }
    }

    protected function decrementStock(array $orderData): void
    {
        foreach ($orderData['products'] as $item) {
            Stock::where([
                'warehouse_id' => $orderData['warehouse_id'],
                'product_id' => $item['product_id']
            ])->decrement('stock', $item['count']);
        }
    }
}
