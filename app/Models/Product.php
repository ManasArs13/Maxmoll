<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * Отключение автоматического управления временными метками
     * @var bool
     */
    public $timestamps = false;

    /**
     * Поля, разрешенные для массового заполнения
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'price'
    ];

    /**
     * Преобразование типов атрибутов
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float'
    ];

    /**
     * Отношение к остаткам на складах
     * @return HasMany
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Отношение к заказам через промежуточную таблицу
     * @return BelongsToMany
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('count'); // Количество товара в заказе
    }

    /**
     * Отношение к движениям товаров
     * @return HasMany
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
