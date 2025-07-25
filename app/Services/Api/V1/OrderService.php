<?php

namespace App\Services\Api\V1;

use App\Builders\Api\V1\OrderQueryBuilder;
use Illuminate\Http\Request;

class OrderService
{
    public function getFilteredOrders(Request $request, ?int $perPage = null)
    {
        return (new OrderQueryBuilder())
            ->applyFilters($request)
            ->paginate($perPage ?? $request->get('per_page', 15));
    }
}