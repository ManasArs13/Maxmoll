<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StockMovementFilterRequest;
use App\Http\Resources\V1\StockMovement\StockMovementResource;
use App\Models\StockMovement;
use App\Services\Api\V1\StockMovementSevice;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementController extends Controller
{
    public function __construct(
        private StockMovementSevice $stockMovementSevice
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(StockMovementFilterRequest $request): JsonResource
    {
        $stockMovement = $this->stockMovementSevice->getFilteredStockMovements($request);
        return StockMovementResource::collection($stockMovement);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement): StockMovementResource
    {
        return new StockMovementResource($stockMovement);
    }
}
