<?php

namespace App\Services\Api\V1;

use App\Contracts\Api\QueryBuilderInterface;
use Illuminate\Http\Request;

class FilterService
{
    /**
     * Получение отфильтрованного списка заказов с пагинацией
     *
     * @param Request $request HTTP-запрос с параметрами фильтрации
     * @param int|null $perPage Количество элементов на странице (null для значения по умолчанию)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilter(QueryBuilderInterface $queryBuilderInterface, Request $request, ?int $perPage = null)
    {
        return ($queryBuilderInterface)
            ->applyFilters($request)
            ->paginate($perPage ?? $request->get('per_page', 15));
    }
}
