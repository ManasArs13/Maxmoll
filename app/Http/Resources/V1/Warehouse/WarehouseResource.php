<?php

namespace App\Http\Resources\V1\Warehouse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'warehouse' => [
                'id' => $this->id,
                'name' => $this->name,
            ],
            'links' => [
                'self' => route('api.v1.warehouses.show', $this->id),
            ]
        ];
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'message' => "Warehouse '$this->name' retrieved successfully",
        ];
    }
}
