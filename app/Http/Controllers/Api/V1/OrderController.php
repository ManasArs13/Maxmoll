<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\Orders\CancelOrderAction;
use App\Actions\Api\V1\Orders\CompleteOrderAction;
use App\Actions\Api\V1\Orders\CreateOrderAction;
use App\Actions\Api\V1\Orders\ReturnOrderAction;
use App\Actions\Api\V1\Orders\UpdateOrderAction;
use App\Models\Order;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CancelOrderRequest;
use App\Http\Requests\Api\V1\ReturnOrderRequest;
use App\Http\Requests\Api\V1\UpdateOrderRequest;
use App\Http\Resources\V1\Order\OrderResource;
use App\Services\Api\V1\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CreateOrderAction $createOrder,
        private UpdateOrderAction $updateOrder,
        private CompleteOrderAction $completeOrder,
        private CancelOrderAction $canceleOrder,
        private ReturnOrderAction $returnOrder
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResource
    {
        $orders = $this->orderService->getFilteredOrders($request);
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->createOrder->apply($request->validated());
        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): OrderResource
    {
        $order->load([
            'warehouse',
            'products' => function ($query) {
                $query->withPivot('count');
            }
        ]);

        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $updatedOrder = $this->updateOrder->apply($order, $request->validated());
        return new OrderResource($updatedOrder);
    }

    /**
     * Complete the specified resource in storage.
     */
    public function complete(Order $order)
    {
        $updatedOrder = $this->completeOrder->apply($order);
        return new OrderResource($updatedOrder);
    }

    /**
     * Cancel the specified resource in storage.
     */
    public function cancel(CancelOrderRequest $request, Order $order)
    {
        $updatedOrder = $this->canceleOrder->apply($order);
        return new OrderResource($updatedOrder);
    }

    /**
     * Return the specified resource in storage.
     */
    public function return(ReturnOrderRequest $request, Order $order)
    {
        $updatedOrder = $this->returnOrder->apply($order);
        return new OrderResource($updatedOrder);
    }
}
