<?php

namespace App\Http\Resources\V1\Order;

use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Resources\V1\Warehouse\WarehouseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order' => [
                'id' => $this->id,
                'customer' => $this->customer,
                'status' => $this->status,
                'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
                'products' => ProductResource::collection($this->whenLoaded('products')),
                'created_at' => $this->created_at?->format('Y-m-d H:i'),
                'completed_at' => $this->completed_at?->format('Y-m-d H:i'),
            ],
            'links' => [
                'self' => route('api.v1.orders.show', $this->id),
            ]
        ];
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => "Order №'$this->id' retrieved successfully",
        ];
    }
}
