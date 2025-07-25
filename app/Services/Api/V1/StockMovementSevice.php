<?php

namespace App\Services\Api\V1;

use App\Builders\Api\V1\StockMovementQueryBuilder;
use Illuminate\Http\Request;

class StockMovementSevice
{
    /**
     * Получение отфильтрованного списка движений товаров с пагинацией
     *
     * @param Request $request HTTP-запрос с параметрами фильтрации:
     *              - product_id: ID товара
     *              - warehouse_id: ID склада
     *              - date_from: Начальная дата периода
     *              - date_to: Конечная дата периода
     *              - type: Тип движения (incoming/outgoing)
     *              - per_page: Количество элементов на странице
     * @param int|null $perPage Количество элементов на странице (переопределяет параметр запроса)
     * @return LengthAwarePaginator Пагинированный список движений товаров
     */
    public function getFilteredStockMovements(Request $request, ?int $perPage = null)
    {
        return (new StockMovementQueryBuilder())
            ->applyFilters($request)
            ->paginate($perPage ?? $request->get('per_page', 15));
    }
}
