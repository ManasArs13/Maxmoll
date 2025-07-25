<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;

    /**
     * Использование составного первичного ключа
     * @var bool
     */
    public $incrementing = false;

    /**
     * Поля составного первичного ключа
     * @var array<string>
     */
    protected $primaryKey = ['warehouse_id', 'product_id'];

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
        'product_id',
        'warehouse_id',
        'stock'
    ];

    /**
     * Преобразование типов атрибутов
     * @var array<string, string>
     */
    protected $casts = [
        'stock' => 'integer',
        'warehouse_id' => 'integer',
        'product_id' => 'integer'
    ];

    /**
     * Отношение к товару
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Отношение к складу
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
