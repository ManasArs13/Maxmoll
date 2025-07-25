<?php

namespace App\Services\Api\V1;

use App\Builders\Api\V1\OrderQueryBuilder;
use Illuminate\Http\Request;

class OrderService
{
    /**
     * Получение отфильтрованного списка заказов с пагинацией
     *
     * @param Request $request HTTP-запрос с параметрами фильтрации
     * @param int|null $perPage Количество элементов на странице (null для значения по умолчанию)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredOrders(Request $request, ?int $perPage = null)
    {
        return (new OrderQueryBuilder())
            ->applyFilters($request)
            ->paginate($perPage ?? $request->get('per_page', 15));
    }
}
