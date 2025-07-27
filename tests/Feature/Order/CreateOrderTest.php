<?php

namespace Tests\Feature\Order;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_order_with_products()
    {
        // Создаем тестовые данные
        $warehouse = Warehouse::factory()->create(['name' => 'Dinoton']);
        $product = Product::factory()->create([
            'name' => 'quis impedit debitis',
            'price' => 457.24
        ]);

        // Создаем остатки на складе
        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'stock' => 10
        ]);

        $requestData = [
            'customer' => 'Ivan Ivanov',
            'warehouse_id' => $warehouse->id,
            'products' => [
                [
                    'product_id' => $product->id,
                    'count' => 2
                ]
            ]
        ];

        // Делаем запрос
        $response = $this->postJson('/api/v1/orders', $requestData);

        // Проверяем ответ
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'order' => [
                        'id',
                        'customer',
                        'status',
                        'warehouse',
                        'products',
                        'created_at',
                        'completed_at'
                    ],
                    'links' => [
                        'self'
                    ]
                ],
                'status',
                'message'
            ])
            ->assertJsonPath('data.order.customer', 'Ivan Ivanov')
            ->assertJsonPath('data.order.status', 'active')
            ->assertJsonPath('data.order.warehouse.warehouse.id', $warehouse->id)
            ->assertJsonPath('data.order.products.0.product.id', $product->id)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', "Order №'{$response['data']['order']['id']}' retrieved successfully");

        // Проверяем, что заказ создан в базе
        $this->assertDatabaseHas('orders', [
            'customer' => 'Ivan Ivanov',
            'warehouse_id' => $warehouse->id,
            'status' => 'active'
        ]);

        // Проверяем связь с продуктами
        $orderId = $response['data']['order']['id'];
        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product->id,
            'count' => 2
        ]);

        // Проверяем обновление остатков
        $this->assertDatabaseHas('stocks', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'stock' => 8 // 10 - 2
        ]);

        // Проверяем запись о движении товара
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => -2,
            'balance_after' => 8
        ]);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer',
                'warehouse_id',
                'products'
            ]);
    }

    #[Test]
    public function it_validates_products_array_not_empty()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->postJson('/api/v1/orders', [
            'customer' => 'Test',
            'warehouse_id' => $warehouse->id,
            'products' => []
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products']);
    }

    #[Test]
    public function it_validates_product_id_exists()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->postJson('/api/v1/orders', [
            'customer' => 'Test',
            'warehouse_id' => $warehouse->id,
            'products' => [
                ['product_id' => 999, 'count' => 1]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.product_id']);
    }

    #[Test]
    public function it_validates_count_is_positive_integer()
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/orders', [
            'customer' => 'Test',
            'warehouse_id' => $warehouse->id,
            'products' => [
                ['product_id' => $product->id, 'count' => 0]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.count']);
    }

    #[Test]
    public function it_fails_when_not_enough_stock()
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'stock' => 1
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer' => 'Test',
            'warehouse_id' => $warehouse->id,
            'products' => [
                ['product_id' => $product->id, 'count' => 2]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJson([
                "code" => 422,
                "message" => [
                    "items" => [
                        "Недостаточно товара ID $warehouse->id на складе"
                    ]
                ],
                "errors" => [
                    "items" => [
                        "Недостаточно товара ID $warehouse->id на складе"
                    ]
                ]
            ]);
    }

    #[Test]
    public function it_creates_multiple_products_in_order()
    {
        $warehouse = Warehouse::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product1->id,
            'stock' => 10
        ]);

        Stock::factory()->create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product2->id,
            'stock' => 5
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer' => 'Multi Product',
            'warehouse_id' => $warehouse->id,
            'products' => [
                ['product_id' => $product1->id, 'count' => 3],
                ['product_id' => $product2->id, 'count' => 2]
            ]
        ]);

        $response->assertStatus(201);

        $orderId = $response['data']['order']['id'];

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product1->id,
            'count' => 3
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product2->id,
            'count' => 2
        ]);

        // Проверяем остатки
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product1->id,
            'stock' => 7
        ]);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product2->id,
            'stock' => 3
        ]);
    }
}
