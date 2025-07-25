<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\CancelOrderAction;
use App\Actions\Api\V1\CompleteOrderAction;
use App\Actions\Api\V1\CreateOrderAction;
use App\Actions\Api\V1\ReturnOrderAction;
use App\Actions\Api\V1\UpdateOrderAction;
use App\Models\Order;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CancelOrderRequest;
use App\Http\Requests\Api\V1\OrderFilterRequest;
use App\Http\Requests\Api\V1\ReturnOrderRequest;
use App\Http\Requests\Api\V1\UpdateOrderRequest;
use App\Http\Resources\V1\Order\OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Builders\Api\V1\OrderQueryBuilder;
use App\Services\Api\V1\FilterService;

class OrderController extends Controller
{
    /**
     * Внедрение зависимостей через конструктор
     *
     * @param FilterService $filterService Сервис фильтрации
     * @param CreateOrderAction $createOrder Действие создания заказа
     * @param UpdateOrderAction $updateOrder Действие обновления заказа
     * @param CompleteOrderAction $completeOrder Действие завершения заказа
     * @param CancelOrderAction $cancelOrder Действие отмены заказа
     * @param ReturnOrderAction $returnOrder Действие возврата заказа
     */
    public function __construct(
        private FilterService $filterService,
        private CreateOrderAction $createOrder,
        private UpdateOrderAction $updateOrder,
        private CompleteOrderAction $completeOrder,
        private CancelOrderAction $canceleOrder,
        private ReturnOrderAction $returnOrder
    ) {}

    /**
     * Получение списка заказов с фильтрацией
     *
     * @param OrderFilterRequest $request Запрос с параметрами фильтрации
     * @return JsonResource Коллекция заказов в формате JSON
     */
    public function index(OrderFilterRequest $request): JsonResource
    {
        // Получаем отфильтрованные заказы через сервис
        $orders = $this->filterService->getFilter(new OrderQueryBuilder, $request);
        
        return OrderResource::collection($orders);
    }

    /**
     * Создание нового заказа
     *
     * @param StoreOrderRequest $request Валидированный запрос с данными заказа
     * @return OrderResource Созданный заказ в формате JSON
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->createOrder->apply($request->validated());
        return new OrderResource($order);
    }

    /**
     * Просмотр конкретного заказа
     *
     * @param Order $order Модель заказа
     * @return OrderResource Заказ с подгруженными связями в формате JSON
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
     * Обновление данных заказа
     *
     * @param UpdateOrderRequest $request Валидированный запрос с новыми данными
     * @param Order $order Модель заказа для обновления
     * @return OrderResource Обновленный заказ в формате JSON
     */
    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $updatedOrder = $this->updateOrder->apply($order, $request->validated());
        return new OrderResource($updatedOrder);
    }

    /**
     * Завершение заказа (перевод в статус "completed")
     *
     * @param Order $order Модель заказа для завершения
     * @return OrderResource Завершенный заказ в формате JSON
     */
    public function complete(Order $order): OrderResource
    {
        $updatedOrder = $this->completeOrder->apply($order);
        return new OrderResource($updatedOrder);
    }

    /**
     * Отмена заказа (перевод в статус "canceled")
     *
     * @param CancelOrderRequest $request Валидированный запрос
     * @param Order $order Модель заказа для отмены
     * @return OrderResource Отмененный заказ в формате JSON
     */
    public function cancel(CancelOrderRequest $request, Order $order): OrderResource
    {
        $updatedOrder = $this->canceleOrder->apply($order);
        return new OrderResource($updatedOrder);
    }

    /**
     * Возврат заказа (перевод в статус "active")
     *
     * @param ReturnOrderRequest $request Валидированный запрос
     * @param Order $order Модель заказа для возврата
     * @return OrderResource Возвращенный заказ в формате JSON
     */
    public function return(ReturnOrderRequest $request, Order $order): OrderResource
    {
        $updatedOrder = $this->returnOrder->apply($order);
        return new OrderResource($updatedOrder);
    }
}
