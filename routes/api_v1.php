<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\StockMovementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\WarehouseController;

/*
|--------------------------------------------------------------------------
| API Routes Version 1
|--------------------------------------------------------------------------
|
| Маршруты для API версии 1. Все маршруты имеют префикс /v1
| и префикс именования api.v1.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Маршруты для работы со складами (только чтение)
    Route::apiResource('warehouses', WarehouseController::class)
        ->only(['index', 'show']);

    // Маршруты для работы с товарами (только чтение)
    Route::apiResource('products', ProductController::class)
        ->only(['index', 'show']);

    // Маршруты для работы с движениями товаров (только чтение)
    Route::apiResource('stock-movements', StockMovementController::class)
        ->only(['index', 'show']);

    // Маршруты для работы с заказами (полный CRUD кроме удаления)
    Route::apiResource('orders', OrderController::class)
        ->only(['index', 'show', 'store', 'update']);

    // Дополнительные маршруты для управления статусами заказов
    Route::prefix('orders/{order}')->group(function () {

        // Завершение заказа
        Route::get('complete', [OrderController::class, 'complete'])
            ->name('orders.complete');

        // Отмена заказа
        Route::get('cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');

        // Возврат заказа
        Route::get('return', [OrderController::class, 'return'])
            ->name('orders.return');
    });
});
