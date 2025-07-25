<?php

namespace App\Services\Api\V1;

use App\Builders\Api\V1\StockMovementQueryBuilder;
use Illuminate\Http\Request;

class StockMovementSevice
{
    public function getFilteredStockMovements(Request $request, ?int $perPage = null)
    {
        return (new StockMovementQueryBuilder())
            ->applyFilters($request)
            ->paginate($perPage ?? $request->get('per_page', 15));
    }
}
