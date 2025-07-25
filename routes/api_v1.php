<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\WarehouseController;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::apiResource('warehouses', WarehouseController::class)
        ->only(['index', 'show']);

    Route::apiResource('products', ProductController::class)
        ->only(['index', 'show']);

    Route::apiResource('orders', OrderController::class)
        ->only(['index', 'show', 'store', 'update']);

    Route::get('/orders/{order}/complete', [OrderController::class, 'complete'])
        ->name('orders.complete');

    Route::get('/orders/{order}/cancel', [OrderController::class, 'cancel'])
        ->name('orders.cancel');

    Route::get('/orders/{order}/return', [OrderController::class, 'return'])
        ->name('orders.return');
});
