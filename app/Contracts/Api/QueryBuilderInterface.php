<?php

namespace App\Contracts\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface QueryBuilderInterface
{
    /**
     * Применяет все фильтры из запроса
     */
    public function applyFilters(Request $request): self;

    /**
     * Возвращает текущий построитель запроса
     */
    public function getQuery(): Builder;

    /**
     * Выполняет запрос с пагинацией
     */
    public function paginate(?int $perPage = null): LengthAwarePaginator;
}