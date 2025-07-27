<?php

namespace App\Builders\Api\V1;

use App\Builders\BaseQueryBuilder;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderQueryBuilder extends BaseQueryBuilder
{
    public function __construct()
    {
        parent::__construct(
            model: Order::class,
            with: ['warehouse', 'products'],
            orderBy: 'created_at',
            orderDirection: 'desc'
        );
    }

    /**
     * Применяет все фильтры из запроса
     *
     * @param Request $request
     * @return self
     */
    public function applyFilters(Request $request): self
    {
        $this->filterByStatus($request)
            ->filterByWarehouse($request)
            ->filterByDateRange($request)
            ->filterByCustomer($request);
        return $this;
    }

    /**
     * Фильтр по статусу заказа
     *
     * @param Request $request
     * @return self
     */
    public function filterByStatus(Request $request): self
    {
        if ($request->has('status')) {
            $this->query->where('status', $request->status);
        }
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
     * Фильтр по имени клиента (поиск по подстроке)
     *
     * @param Request $request
     * @return self
     */
    public function filterByCustomer(Request $request): self
    {
        if ($request->has('customer')) {
            $this->query->where('customer', 'like', '%' . $request->customer . '%');
        }
        return $this;
    }
}
