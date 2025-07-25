<?php

namespace App\Builders\Api\V1;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementQueryBuilder
{
    /** @var Builder $query Построитель запроса */
    private $query;

    public function __construct()
    {
        $this->query = StockMovement::with(['warehouse', 'product'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Применяет все фильтры из запроса
     *
     * @param Request $request
     * @return self
     */
    public function applyFilters(Request $request): self
    {
        $this->filterByWarehouse($request)
            ->filterByProduct($request)
            ->filterByDateRange($request);
        return $this;
    }

    /**
     * Фильтр по складу
     *
     * @param Request $request
     * @return self
     */
    public function filterByWarehouse(Request $request): self
    {
        if ($request->has('warehouse')) {
            $this->query->where('warehouse_id', $request->warehouse);
        }
        return $this;
    }

    /**
     * Фильтр по товару
     *
     * @param Request $request
     * @return self
     */
    public function filterByProduct(Request $request): self
    {
        if ($request->has('product')) {
            $this->query->where('product_id', $request->product);
        }
        return $this;
    }

    /**
     * Фильтр по диапазону дат
     *
     * @param Request $request
     * @return self
     */
    public function filterByDateRange(Request $request): self
    {
        if ($request->has('date_from')) {
            $this->query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $this->query->whereDate('created_at', '<=', $request->date_to);
        }
        return $this;
    }

    /**
     * Выполняет запрос с пагинацией
     *
     * @param int|null $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(?int $perPage = null)
    {
        return $this->query->paginate($perPage);
    }

    /**
     * Возвращает текущий построитель запроса
     *
     * @return Builder
     */
    public function getQuery()
    {
        return $this->query;
    }
}
