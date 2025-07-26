<?php

namespace App\Actions\Api\V1;

use App\Events\DecrementStockEvent;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class UpdateOrderAction
{
    /**
     * Обновляет данные заказа и связанные товары:
     * - Обновляет основные данные заказа (клиент, склад)
     * - При изменении состава товаров:
     *   - Возвращает старые товары на склад
     *   - Добавляет новые товары в заказ
     *   - Уменьшает остатки новых товаров на складе
     *
     * @param Order $order Заказ для обновления
     * @param array $orderData Новые данные заказа:
     *               - customer?: Новое имя клиента (опционально)
     *               - warehouse_id?: Новый ID склада (опционально)
     *               - products?: Новый список товаров (опционально)
     * @return Order Обновленный заказ с подгруженными связями
     * @throws \Throwable При ошибке выполнения транзакции
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если товар не найден
     */
    public function apply(Order $order, array $orderData): Order
    {
        return DB::transaction(function () use ($order, $orderData) {

            $updateData = [];

            foreach (['customer', 'warehouse_id'] as $field) {
                if (array_key_exists($field, $orderData)) {
                    $updateData[$field] = $orderData[$field];
                }
            }

            if ($updateData) {
                $order->update($updateData);
            }

            if (isset($orderData['products'])) {

                // Удаляем старые продукты и добаляем их на склад
                $this->deleteProducts($order);

                // Добавляем новые и убираем со склада
                $this->addItemsToOrder($order, $orderData['products']);

                // Событие для уменьшения остатки на складе
                event(new DecrementStockEvent($order));
            }


            return $order->load(['warehouse', 'products']);
        });
    }

    /**
     * Удаляет все товары из заказа и возвращает их на склад
     *
     * @param Order $order Заказ для обработки
     * @return void
     */
    protected function deleteProducts($order): void
    {
        $products = $order->products()->get();

        foreach ($products as $item) {
            Stock::where([
                'warehouse_id' => $order->warehouse_id,
                'product_id' => $item->id,
            ])->increment('stock', $item->pivot->count);
        }

        $order->products()->detach();
    }

    /**
     * Добавляет товары в заказ через промежуточную таблицу
     *
     * @param Order $order Заказ для обновления
     * @param array $items Массив товаров для добавления
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Если товар не найден
     */
    protected function addItemsToOrder(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $order->products()->attach(
                Product::findOrFail($item['product_id']),
                ['count' => $item['count']]
            );
        }
    }
}
