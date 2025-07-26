<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     summary="Получить список заказов с пагинацией и фильтром",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Фильтр по статусу <br/>Возможные варианты: </br> ['active', 'completed', 'canceled']",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Фильтр по дате до (format: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Фильтр по дате от (format: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="warehouse_id",
     *         in="query",
     *         description="Фильтр по складу ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="customer",
     *         in="query",
     *         description="Фильтр по имени покупателя",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Номер страницы пагинации",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=66),
     *                         @OA\Property(property="customer", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="status", type="string", example="active"),
     *                         @OA\Property(
     *                             property="warehouse",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="warehouse",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Dinoton")
     *                             ),
     *                             @OA\Property(
     *                                 property="links",
     *                                 type="object",
     *                                 @OA\Property(property="self", type="string", example="https://max/api/v1/warehouses/3")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="products",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="product",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=2),
     *                                     @OA\Property(property="name", type="string", example="quis impedit debitis"),
     *                                     @OA\Property(property="price", type="number", format="float", example=457.24)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="links",
     *                                     type="object",
     *                                     @OA\Property(property="self", type="string", example="https://max/api/v1/products/2")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 09:28"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null)
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/66")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="https://max/api/v1/orders?page=1"),
     *                 @OA\Property(property="last", type="string", example="https://max/api/v1/orders?page=2"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", example="https://max/api/v1/orders?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=2),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example=null),
     *                         @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="https://max/api/v1/orders"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=20)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid date format")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     summary="Создание заказа",
     *     tags={"Заказы"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Order data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"customer", "warehouse_id", "products"},
     *                 @OA\Property(
     *                     property="customer",
     *                     type="string",
     *                     example="Иван Иванов",
     *                     maxLength=255
     *                 ),
     *                 @OA\Property(
     *                     property="warehouse_id",
     *                     type="integer",
     *                     example=3
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     minItems=1,
     *                     @OA\Items(
     *                         required={"product_id", "count"},
     *                         @OA\Property(
     *                             property="product_id",
     *                             type="integer",
     *                             example=2
     *                         ),
     *                         @OA\Property(
     *                             property="count",
     *                             type="integer",
     *                             example=1,
     *                             minimum=1
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=67),
     *                         @OA\Property(property="customer", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="status", type="string", example="active"),
     *                         @OA\Property(
     *                             property="warehouse",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="warehouse",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Dinoton")
     *                             ),
     *                             @OA\Property(
     *                                 property="links",
     *                                 type="object",
     *                                 @OA\Property(property="self", type="string", example="https://max/api/v1/warehouses/3")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="products",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="product",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=2),
     *                                     @OA\Property(property="name", type="string", example="quis impedit debitis"),
     *                                     @OA\Property(property="price", type="number", format="float", example=457.24)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="links",
     *                                     type="object",
     *                                     @OA\Property(property="self", type="string", example="https://max/api/v1/products/2")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 09:40"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null)
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/67")
     *                     )
     *                 ),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Order №'67' retrieved successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="message", type="string", example="The given data was invalid."),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="customer",
     *                         type="array",
     *                         @OA\Items(type="string", example="The customer field is required.")
     *                     ),
     *                     @OA\Property(
     *                         property="warehouse_id",
     *                         type="array",
     *                         @OA\Items(type="string", example="The selected warehouse id is invalid.")
     *                     ),
     *                     @OA\Property(
     *                         property="products",
     *                         type="array",
     *                         @OA\Items(type="string", example="The products field is required.")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     summary="Получить заказ по ID",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID заказа",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=68),
     *                         @OA\Property(property="customer", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="status", type="string", example="active"),
     *                         @OA\Property(
     *                             property="warehouse",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="warehouse",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Dinoton")
     *                             ),
     *                             @OA\Property(
     *                                 property="links",
     *                                 type="object",
     *                                 @OA\Property(property="self", type="string", example="https://max/api/v1/warehouses/3")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="products",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="product",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=2),
     *                                     @OA\Property(property="name", type="string", example="quis impedit debitis"),
     *                                     @OA\Property(property="price", type="number", format="float", example=457.24)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="links",
     *                                     type="object",
     *                                     @OA\Property(property="self", type="string", example="https://max/api/v1/products/2")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 09:44"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null)
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/68")
     *                     )
     *                 ),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Order №'68' retrieved successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Order not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function show() {}

    /**
     * @OA\Patch(
     *     path="/api/v1/orders/{id}",
     *     summary="Обновить заказ",
     *     description="обновление информации о заказе",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Заказа",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Данные для обновления",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="customer",
     *                     type="string",
     *                     maxLength=255,
     *                     example="Иван Иванов"
     *                 ),
     *                 @OA\Property(
     *                     property="warehouse_id",
     *                     type="integer",
     *                     example=3
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     minItems=1,
     *                     @OA\Items(
     *                         @OA\Property(
     *                             property="product_id",
     *                             type="integer",
     *                             example=2
     *                         ),
     *                         @OA\Property(
     *                             property="count",
     *                             type="integer",
     *                             minimum=1,
     *                             example=1
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=68),
     *                         @OA\Property(property="customer", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="status", type="string", example="active"),
     *                         @OA\Property(
     *                             property="warehouse",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="warehouse",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Dinoton")
     *                             ),
     *                             @OA\Property(
     *                                 property="links",
     *                                 type="object",
     *                                 @OA\Property(property="self", type="string", example="https://max/api/v1/warehouses/3")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="products",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="product",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=2),
     *                                     @OA\Property(property="name", type="string", example="quis impedit debitis"),
     *                                     @OA\Property(property="price", type="number", format="float", example=457.24)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="links",
     *                                     type="object",
     *                                     @OA\Property(property="self", type="string", example="https://max/api/v1/products/2")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 09:44"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null)
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/68")
     *                     )
     *                 ),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Order №'68' updated successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Invalid input data"),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="warehouse_id",
     *                         type="array",
     *                         @OA\Items(type="string", example="The selected warehouse id is invalid.")
     *                     ),
     *                     @OA\Property(
     *                         property="products.0.product_id",
     *                         type="array",
     *                         @OA\Items(type="string", example="The selected products.0.product_id is invalid.")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Order not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function update() {}

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{order}/complete",
     *     summary="Завершение активного заказа",
     *     description="Изменение статуса заказа с 'active' на 'completed'",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="ID of the order to complete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order completed successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=68),
     *                         @OA\Property(property="customer", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="status", type="string", example="completed"),
     *                         @OA\Property(
     *                             property="warehouse",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="warehouse",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Dinoton")
     *                             ),
     *                             @OA\Property(
     *                                 property="links",
     *                                 type="object",
     *                                 @OA\Property(property="self", type="string", example="https://max/api/v1/warehouses/3")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="products",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="product",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=2),
     *                                     @OA\Property(property="name", type="string", example="quis impedit debitis"),
     *                                     @OA\Property(property="price", type="number", format="float", example=457.24)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="links",
     *                                     type="object",
     *                                     @OA\Property(property="self", type="string", example="https://max/api/v1/products/2")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 09:44"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", example="2025-07-26 09:44")
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/68")
     *                     )
     *                 ),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Order №'68' completed successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot complete order",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Cannot complete order"),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="array",
     *                         @OA\Items(
     *                             oneOf={
     *                                 @OA\Schema(type="string", example="Only active orders can be completed."),
     *                                 @OA\Schema(type="string", example="This order has already been completed.")
     *                             }
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Order not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function complete() {}

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{order}/cancel",
     *     summary="Отмена активного заказа",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="ID of the order to cancel",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=68),
     *                         @OA\Property(property="customer", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="status", type="string", example="canceled"),
     *                         @OA\Property(
     *                             property="warehouse",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="warehouse",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Dinoton")
     *                             ),
     *                             @OA\Property(
     *                                 property="links",
     *                                 type="object",
     *                                 @OA\Property(property="self", type="string", example="https://max/api/v1/warehouses/3")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="products",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="product",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=2),
     *                                     @OA\Property(property="name", type="string", example="quis impedit debitis"),
     *                                     @OA\Property(property="price", type="number", format="float", example=457.24)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="links",
     *                                     type="object",
     *                                     @OA\Property(property="self", type="string", example="https://max/api/v1/products/2")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 09:44"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null)
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/68")
     *                     )
     *                 ),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Order №'68' cancelled successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot cancel order",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Cannot cancel order"),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="array",
     *                         @OA\Items(
     *                             oneOf={
     *                                 @OA\Schema(type="string", example="Only active orders can be canceled."),
     *                                 @OA\Schema(type="string", example="This order has already been cancelled.")
     *                             }
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Order not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function cancel() {}

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{order}/return",
     *     summary="Возврат неактивного заказа в работу",
     *     description="Меняет статус заказа с 'canceled' на 'completed'",
     *     tags={"Заказы"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="ID of the order to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order returned successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=68),
     *                         @OA\Property(property="customer", type="string", example="Иван Иванов"),
     *                         @OA\Property(property="status", type="string", example="completed"),
     *                         @OA\Property(
     *                             property="warehouse",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="warehouse",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=3),
     *                                 @OA\Property(property="name", type="string", example="Dinoton")
     *                             ),
     *                             @OA\Property(
     *                                 property="links",
     *                                 type="object",
     *                                 @OA\Property(property="self", type="string", example="https://max/api/v1/warehouses/3")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="products",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="product",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=2),
     *                                     @OA\Property(property="name", type="string", example="quis impedit debitis"),
     *                                     @OA\Property(property="price", type="number", format="float", example=457.24)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="links",
     *                                     type="object",
     *                                     @OA\Property(property="self", type="string", example="https://max/api/v1/products/2")
     *                                 )
     *                             )
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 09:44"),
     *                         @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, example=null)
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/68")
     *                     )
     *                 ),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Order №'68' returned to completed status successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cannot return order",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Cannot return order"),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="order",
     *                         type="array",
     *                         @OA\Items(
     *                             oneOf={
     *                                 @OA\Schema(type="string", example="Only canceled orders can be returned."),
     *                                 @OA\Schema(type="string", example="This order is already active.")
     *                             }
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Order not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function return() {}
}
