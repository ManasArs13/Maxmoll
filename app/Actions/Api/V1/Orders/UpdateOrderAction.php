<?php

namespace App\Actions\Api\V1\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class UpdateOrderAction
{
    public function apply(Order $order, array $orderData): Order
    {
        return DB::transaction(function () use ($order, $orderData) {

            $updateData = [];

            foreach (['customer', 'warehouse_id'] as $field) {
                if (array_key_exists($field, $orderData)) {
                    $updateData[$field] = $orderData[$field];
                }
            }

            if ($updateData) {
                $order->update($updateData);
            }

            if (isset($orderData['products'])) {

                // Удаляем старые продукты и добаляем их на склад
                $this->deleteProducts($order);

                // Добавляем новые и убираем со склада
                $this->addItemsToOrder($order, $orderData['products']);
                $this->decrementStock($orderData);
            }


            return $order->load(['warehouse', 'products']);
        });
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

    protected function deleteProducts($order): void
    {
        $products = $order->products()->get();

        foreach ($products as $item) {
              Stock::where([
                'warehouse_id' => $order->warehouse_id,
                'product_id' => $item->id,
            ])->increment('stock', $item->pivot->count);
        }

        $order->products()->detach();
    }
}
