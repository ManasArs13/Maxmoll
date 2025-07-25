<?php

namespace App\Http\Resources\V1\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'StockMovement' => [
                'id' => $this->id,
                'product' => $this->product->name,
                'warehouse' => $this->warehouse->name,
                'quantity' => $this->quantity,
                'balance_after' => $this->balance_after,
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
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
            'message' => "StockMovement №'$this->id' retrieved successfully",
        ];
    }
}
