<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Warehouse\WarehouseCollection;
use App\Http\Resources\V1\Warehouse\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): WarehouseCollection
    {
        $warehouses = Warehouse::all();

        return new WarehouseCollection($warehouses);
    }


    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse): WarehouseResource
    {
        return new WarehouseResource($warehouse);
    }
}
