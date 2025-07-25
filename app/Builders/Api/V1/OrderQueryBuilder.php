<?php

namespace App\Builders\Api\V1;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderQueryBuilder
{
    private $query;

    public function __construct()
    {
        $this->query = Order::with(['warehouse', 'products'])
            ->orderBy('created_at', 'desc');
    }

    public function applyFilters(Request $request): self
    {
        $this->filterByStatus($request)
            ->filterByWarehouse($request)
            ->filterByDateRange($request)
            ->filterByCustomer($request);

        return $this;
    }

    public function filterByStatus(Request $request): self
    {
        if ($request->has('status')) {
            $this->query->where('status', $request->status);
        }
        return $this;
    }

    public function filterByWarehouse(Request $request): self
    {
        if ($request->has('warehouse_id')) {
            $this->query->where('warehouse_id', $request->warehouse_id);
        }
        return $this;
    }

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

    public function filterByCustomer(Request $request): self
    {
        if ($request->has('customer')) {
            $this->query->where('customer', 'like', '%' . $request->customer . '%');
        }
        return $this;
    }

    public function paginate(?int $perPage = null)
    {
        return $this->query->paginate($perPage);
    }

    public function getQuery()
    {
        return $this->query;
    }
}
