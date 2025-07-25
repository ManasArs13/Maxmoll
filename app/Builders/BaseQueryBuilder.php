<?php

namespace App\Builders;

use App\Contracts\Api\QueryBuilderInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseQueryBuilder implements QueryBuilderInterface
{
    /** @var Builder $query Построитель запроса */
    protected Builder $query;

    /**
     * @param Model|string $model Модель или класс модели для построения запроса
     * @param array $with Отношения для жадной загрузки
     * @param string $orderBy Поле для сортировки
     * @param string $orderDirection Направление сортировки
     */
    public function __construct(
        Model|string $model,
        array $with = [],
        string $orderBy = 'created_at',
        string $orderDirection = 'desc'
    ) {
        $this->query = $model::with($with)->orderBy($orderBy, $orderDirection);
    }

    /**
     * Применяет все фильтры из запроса
     */
    public function applyFilters(Request $request): self
    {
        return $this;
    }

    /**
     * Возвращает текущий построитель запроса
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Выполняет запрос с пагинацией
     */
    public function paginate(?int $perPage = null): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }
}
