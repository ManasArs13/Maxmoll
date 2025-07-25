<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovement;

class DatabaseSeeder extends Seeder
{
    // Конфигурация количества записей
    protected const PRODUCTS_COUNT = 10;
    protected const WAREHOUSES_COUNT = 3;
    protected const ORDERS_COUNT = 20;
    protected const MIN_STOCK = 10;
    protected const MAX_STOCK = 100;
    protected const MIN_ORDER_ITEMS = 1;
    protected const MAX_ORDER_ITEMS = 5;
    protected const MIN_PRODUCT_QUANTITY = 1;
    protected const MAX_PRODUCT_QUANTITY = 5;
    protected const STOCK_MOVEMENT_COUNT = 30;

    // Вероятности статусов заказов (%)
    protected const COMPLETED_ORDER_CHANCE = 30;
    protected const CANCELED_ORDER_CHANCE = 10;


    /**
     * Seed the application's database.
     */
    public function run()
    {
        $products = Product::factory()->count(self::PRODUCTS_COUNT)->create();
        $warehouses = Warehouse::factory()->count(self::WAREHOUSES_COUNT)->create();

        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                Stock::factory()->create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'stock' => rand(self::MIN_STOCK, self::MAX_STOCK),
                ]);
            }
        }

        Order::factory()->count(self::ORDERS_COUNT)->create([
            'warehouse_id' => fn() => $warehouses->random()->id,
        ])->each(function ($order) use ($products) {
            $productsToAttach = $products->random(rand(
                self::MIN_ORDER_ITEMS,
                self::MAX_ORDER_ITEMS
            ));

            $order->products()->attach(
                $productsToAttach->pluck('id')->toArray(),
                ['count' => rand(
                    self::MIN_PRODUCT_QUANTITY,
                    self::MAX_PRODUCT_QUANTITY
                )]
            );

            $this->setOrderStatus($order);
        });

        StockMovement::factory()->count(self::STOCK_MOVEMENT_COUNT)->create([
            'warehouse_id' => fn() => $warehouses->random()->id,
            'product_id' => fn() => $products->random()->id,
        ]);

        $this->printStatistics();
    }

    /**
     * Установка случайного статуса для заказа
     *
     * @param Order $order
     */
    protected function setOrderStatus($order)
    {
        $chance = rand(1, 100);

        if ($chance <= self::COMPLETED_ORDER_CHANCE) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now()->subDays(rand(1, 30)),
            ]);
        } elseif ($chance <= (self::COMPLETED_ORDER_CHANCE + self::CANCELED_ORDER_CHANCE)) {
            $order->update(['status' => 'canceled']);
        }
    }

    /**
     * Вывод статистики по заполненным данным
     */
    protected function printStatistics()
    {
        $this->command->info('Database seeded successfully!');
        $this->command->table(
            ['Таблица', 'Всего записей', 'Выполнено сейчас'],
            [
                ['Продукты', Product::count(), self::PRODUCTS_COUNT],
                ['Склады', Warehouse::count(), self::WAREHOUSES_COUNT],
                ['Остатки', Stock::count(), self::PRODUCTS_COUNT * self::WAREHOUSES_COUNT],
                ['Заказы', Order::count(), self::ORDERS_COUNT],
                ['Движение товаров', StockMovement::count(), self::STOCK_MOVEMENT_COUNT]
            ]
        );
    }
}
