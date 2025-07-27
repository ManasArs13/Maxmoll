<?php

namespace Tests\Feature\Order;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_orders_with_default_pagination()
    {
        $this->artisan('db:seed');

        $response = $this->getJson('/api/v1/orders');

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'order' => [
                            'id',
                            'customer',
                            'status',
                            'warehouse',
                            'products',
                            'created_at',
                            'completed_at',
                        ],
                        'links' => [
                            'self',
                        ],
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ])
            ->assertJsonCount(15, 'data') // Проверяем дефолтное количество на странице
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.total', DatabaseSeeder::ORDERS_COUNT);
    }

    #[Test]
    public function it_can_filter_orders_by_status()
    {
        $this->artisan('db:seed');

        // Делаем запрос с фильтром
        $response = $this->getJson('/api/v1/orders?status=active');

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonPath('data.0.order.status', 'active');
    }

    #[Test]
    public function it_can_filter_orders_by_warehouse_id()
    {
        // Создаем тестовые данные
        $warehouse1 = DB::table('warehouses')->insertGetId([
            'name' => 'Warehouse 1',
        ]);

        $warehouse2 = DB::table('warehouses')->insertGetId([
            'name' => 'Warehouse 2',
        ]);

        DB::table('orders')->insert([
            ['customer' => 'Customer 1', 'status' => 'active', 'warehouse_id' => $warehouse1, 'created_at' => now()],
            ['customer' => 'Customer 2', 'status' => 'active', 'warehouse_id' => $warehouse2, 'created_at' => now()],
        ]);

        // Делаем запрос с фильтром
        $response = $this->getJson("/api/v1/orders?warehouse=$warehouse1");

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order.warehouse.warehouse.id', $warehouse1);
    }

    #[Test]
    public function it_can_filter_orders_by_date_range()
    {
        // Создаем тестовые данные
        $warehouse = DB::table('warehouses')->insertGetId([
            'name' => 'Test Warehouse',
        ]);

        DB::table('orders')->insert([
            ['customer' => 'Customer 1', 'status' => 'active', 'warehouse_id' => $warehouse, 'created_at' => '2025-07-01 00:00:00'],
            ['customer' => 'Customer 2', 'status' => 'active', 'warehouse_id' => $warehouse, 'created_at' => '2025-07-15 00:00:00'],
            ['customer' => 'Customer 3', 'status' => 'active', 'warehouse_id' => $warehouse, 'created_at' => '2025-07-31 00:00:00'],
        ]);

        // Делаем запрос с фильтром по дате
        $response = $this->getJson('/api/v1/orders?date_from=2025-07-10&date_to=2025-07-20');

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonPath('data.0.order.customer', 'Customer 2');
    }

    #[Test]
    public function it_can_filter_orders_by_customer_name()
    {
        // Создаем тестовые данные
        $warehouse = DB::table('warehouses')->insertGetId([
            'name' => 'Test Warehouse',
        ]);

        DB::table('orders')->insert([
            ['customer' => 'Иван Иванов', 'status' => 'active', 'warehouse_id' => $warehouse, 'created_at' => now()],
            ['customer' => 'Петр Петров', 'status' => 'active', 'warehouse_id' => $warehouse, 'created_at' => now()],
        ]);

        // Делаем запрос с фильтром
        $response = $this->getJson('/api/v1/orders?customer=Иван');

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order.customer', 'Иван Иванов');
    }

    #[Test]
    public function it_can_change_per_page_parameter()
    {
        $this->artisan('db:seed');

        // Делаем запрос с измененным количеством на странице
        $response = $this->getJson('/api/v1/orders?per_page=5');

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5);
    }

    #[Test]
    public function it_returns_validation_error_for_invalid_status()
    {
        $this->artisan('db:seed');

        // Делаем запрос с невалидным статусом
        $response = $this->getJson('/api/v1/orders?status=invalid_status');

        // Проверяем ответ
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    #[Test]
    public function it_returns_validation_error_for_invalid_warehouse_id()
    {
        $this->artisan('db:seed');

        // Делаем запрос с несуществующим warehouse_id
        $response = $this->getJson('/api/v1/orders?warehouse=999');

        // Проверяем ответ
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['warehouse']);
    }

    #[Test]
    public function it_returns_validation_error_for_invalid_date_format()
    {
        $this->artisan('db:seed');

        // Делаем запрос с невалидным форматом даты
        $response = $this->getJson('/api/v1/orders?date_from=2025/07/01');

        // Проверяем ответ
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date_from']);
    }

    #[Test]
    public function it_returns_validation_error_for_invalid_per_page_value()
    {
        $this->artisan('db:seed');
        
        // Делаем запрос с невалидным значением per_page
        $response = $this->getJson('/api/v1/orders?per_page=101');

        // Проверяем ответ
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }
}
