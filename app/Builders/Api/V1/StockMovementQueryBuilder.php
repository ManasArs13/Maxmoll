<?php

namespace App\Builders\Api\V1;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementQueryBuilder
{
    private $query;

    public function __construct()
    {
        $this->query = StockMovement::with(['warehouse', 'product'])
            ->orderBy('created_at', 'desc');
    }

    public function applyFilters(Request $request): self
    {
        $this->filterByWarehouse($request)
            ->filterByProduct($request)
            ->filterByDateRange($request);
        return $this;
    }

    public function filterByWarehouse(Request $request): self
    {
        if ($request->has('warehouse')) {
            $this->query->where('warehouse_id', $request->warehouse);
        }
        return $this;
    }

    public function filterByProduct(Request $request): self
    {
        if ($request->has('product')) {
            $this->query->where('product_id', $request->product);
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

    public function paginate(?int $perPage = null)
    {
        return $this->query->paginate($perPage);
    }

    public function getQuery()
    {
        return $this->query;
    }
}
