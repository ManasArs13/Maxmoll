<?php

namespace App\Actions\Api\V1\Orders;

use App\Models\Order;

class CompleteOrderAction
{
    public function apply(Order $order): Order
    {
        $order->update([
            'status' => 'completed'
        ]);

        return $order->load(['warehouse', 'products']);
    }
}
