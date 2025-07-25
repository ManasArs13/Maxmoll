<?php

namespace App\Http\Resources\V1\Product;

use App\Http\Resources\V1\Stock\StockResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'price' => $this->price,
                'stocks' => StockResource::collection($this->whenLoaded('stocks')),
            ],
            'links' => [
                'self' => route('api.v1.products.show', $this->id),
            ]
        ];
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => "Product '$this->name' retrieved successfully",
        ];
    }
}
