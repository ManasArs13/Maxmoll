<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

class StockMovementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/stock-movements",
     *     summary="Получить движение товаров с фильтром и пагинацией",
     *     tags={"Движение товаров"},
     *     @OA\Parameter(
     *         name="warehouse_id",
     *         in="query",
     *         description="Filter by warehouse ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Filter by product ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order_id",
     *         in="query",
     *         description="Filter by order ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter movements created after this date (format: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter movements created before this date (format: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="customer",
     *         in="query",
     *         description="Filter by customer name",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (1-100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
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
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="StockMovement",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=74),
     *                             @OA\Property(property="product", type="string", example="quis impedit debitis"),
     *                             @OA\Property(property="warehouse", type="string", example="Dinoton"),
     *                             @OA\Property(property="quantity", type="integer", example=4),
     *                             @OA\Property(property="balance_after", type="integer", example=111088),
     *                             @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-26 10:00:43")
     *                         ),
     *                         @OA\Property(
     *                             property="links",
     *                             type="object",
     *                             @OA\Property(property="self", type="string", example="https://max/api/v1/orders/74")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="first", type="string", example="https://max/api/v1/stock-movements?page=1"),
     *                     @OA\Property(property="last", type="string", example="https://max/api/v1/stock-movements?page=5"),
     *                     @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                     @OA\Property(property="next", type="string", example="https://max/api/v1/stock-movements?page=2")
     *                 ),
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(
     *                         property="links",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="url", type="string", nullable=true),
     *                             @OA\Property(property="label", type="string"),
     *                             @OA\Property(property="active", type="boolean")
     *                         )
     *                     ),
     *                     @OA\Property(property="path", type="string", example="https://max/api/v1/stock-movements"),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="to", type="integer", example=15),
     *                     @OA\Property(property="total", type="integer", example=74)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="message", type="string", example="Invalid date format"),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="date_from",
     *                         type="array",
     *                         @OA\Items(type="string", example="The date_from does not match the format Y-m-d.")
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
     *                 @OA\Property(property="message", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Get(
     *     path="/api/v1/stock-movements/{stock_movement}",
     *     summary="Получить движение по ID",
     *     tags={"Движение товаров"},
     *     @OA\Parameter(
     *         name="stock_movement",
     *         in="path",
     *         description="ID of the stock movement",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stock movement retrieved successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(
     *                         property="StockMovement",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="product", type="string", example="quia repudiandae perferendis"),
     *                         @OA\Property(property="warehouse", type="string", example="Dinoton"),
     *                         @OA\Property(property="quantity", type="integer", example=884),
     *                         @OA\Property(property="balance_after", type="integer", example=326),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-25 10:23:47")
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         type="object",
     *                         @OA\Property(property="self", type="string", example="https://max/api/v1/orders/1")
     *                     )
     *                 ),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="StockMovement №'1' retrieved successfully")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stock movement not found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Stock movement not found")
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
}
