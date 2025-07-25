<?php

namespace App\Http\Controllers\Api\V1;

use App\Builders\Api\V1\StockMovementQueryBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StockMovementFilterRequest;
use App\Http\Resources\V1\StockMovement\StockMovementResource;
use App\Models\StockMovement;
use App\Services\Api\V1\FilterService;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementController extends Controller
{
    /**
     * Конструктор с внедрением зависимостей
     *
     * @param FilterService $filterService Сервис фильтрации
     */
    public function __construct(
        private FilterService $filterService,
    ) {}

    /**
     * Получение отфильтрованного списка движений товаров
     *
     * @param StockMovementFilterRequest $request Запрос с параметрами фильтрации
     * @return JsonResource Коллекция движений товаров в формате JSON
     */
    public function index(StockMovementFilterRequest $request): JsonResource
    {
        // Получаем отфильтрованные движения товаров через сервис
        $stockMovement = $this->filterService->getFilter(new StockMovementQueryBuilder, $request);
        
        return StockMovementResource::collection($stockMovement);
    }

    /**
     * Получение детальной информации о конкретном движении товара
     *
     * @param StockMovement $stockMovement Модель движения товара
     * @return StockMovementResource Ресурс движения товара в формате JSON
     */
    public function show(StockMovement $stockMovement): StockMovementResource
    {
        return new StockMovementResource($stockMovement);
    }
}
