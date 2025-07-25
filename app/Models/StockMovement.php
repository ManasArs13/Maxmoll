<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    /** @use HasFactory<\Database\Factories\StockMovementFactory> */
    use HasFactory;

    /**
     * Поля, разрешенные для массового заполнения
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'balance_after',
        'created_at',
        'updated_at'
    ];

    /**
     * Преобразование типов атрибутов
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'balance_after' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
