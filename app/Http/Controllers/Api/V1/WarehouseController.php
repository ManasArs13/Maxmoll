<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Warehouse\WarehouseCollection;
use App\Http\Resources\V1\Warehouse\WarehouseResource;
use App\Models\Warehouse;


class WarehouseController extends Controller
{
    /**
     * Получение списка всех складов
     *
     * Возвращает полный перечень складов в системе
     * с базовой информацией о каждом складе
     *
     * @return WarehouseCollection Коллекция складов в формате JSON
     */
    public function index(): WarehouseCollection
    {
        $warehouses = Warehouse::all();
        return new WarehouseCollection($warehouses);
    }

    /**
     * Получение детальной информации о конкретном складе
     *
     * @param Warehouse $warehouse Модель запрашиваемого склада (автоматическое разрешение)
     * @return WarehouseResource Ресурс склада в формате JSON
     */
    public function show(Warehouse $warehouse): WarehouseResource
    {
        return new WarehouseResource($warehouse);
    }
}
