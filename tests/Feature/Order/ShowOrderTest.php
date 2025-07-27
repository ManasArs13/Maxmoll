<?php

namespace Tests\Feature\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowOrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_show_an_order_with_products_and_warehouse()
    {
        $this->artisan('db:seed');

        // Делаем запрос
        $response = $this->getJson("/api/v1/orders/1");

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'order' => [
                        'id',
                        'customer',
                        'status',
                        'warehouse' => [
                            'warehouse' => [
                                'id',
                                'name',
                            ],
                            'links' => [
                                'self',
                            ],
                        ],
                        'products' => [
                            '*' => [
                                'product' => [
                                    'id',
                                    'name',
                                    'price',
                                ],
                                'links' => [
                                    'self',
                                ],
                            ],
                        ],
                        'created_at',
                        'completed_at',
                    ],
                    'links' => [
                        'self',
                    ],
                ],
                'status',
                'message',
            ]);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_order()
    {
        // Делаем запрос к несуществующему заказу
        $response = $this->getJson('/api/v1/orders/999');

        // Проверяем ответ
        $response->assertStatus(404)
            ->assertJson([
                "code" => 404,
                "message" => "Record not found."
            ]);
    }
}
