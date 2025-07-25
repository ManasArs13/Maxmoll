<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Product\ProductCollection;
use App\Http\Resources\V1\Product\ProductResource;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Получение списка всех товаров с информацией о наличии на складах
     *
     * Возвращает коллекцию товаров с подгруженными данными:
     * - Информация о наличии на складах (stocks)
     * - Данные складов, где есть товар (warehouse)
     *
     * @return ProductCollection Коллекция товаров в формате JSON
     */
    public function index(): ProductCollection
    {
        $products = Product::with(['stocks.warehouse'])->get();
        return new ProductCollection($products);
    }

    /**
     * Получение детальной информации о конкретном товаре
     *
     * @param Product $product Модель запрашиваемого товара
     * @return ProductResource Ресурс товара в формате JSON
     */
    public function show(Product $product): JsonResponse|ProductResource
    {
        return new ProductResource($product);
    }
}
