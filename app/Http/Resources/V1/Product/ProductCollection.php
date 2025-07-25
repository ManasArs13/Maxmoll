<?php

namespace App\Http\Resources\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
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
                'self' => route('api.v1.products.index'),
            ]
        ];
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => 'Products retrieved successfully',
        ];
    }
}
