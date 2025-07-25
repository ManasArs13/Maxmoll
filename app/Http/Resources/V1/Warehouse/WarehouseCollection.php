<?php

namespace App\Http\Resources\V1\Warehouse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WarehouseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->count(),
            ],
            'links' => [
                'self' => route('api.v1.warehouses.index'),
            ]
        ];
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => 'Warehouses retrieved successfully',
        ];
    }
}
