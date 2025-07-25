<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
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
        'customer',
        'created_at',
        'completed_at',
        'warehouse_id',
        'status'
    ];

    /**
     * Преобразование типов атрибутов
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Отношение к складу
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Отношение к товарам через промежуточную таблицу
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('count');
    }

    /**
     * Обработчики событий модели
     * 
     * Автоматическая установка даты завершения при изменении статуса на "completed"
     * Сброс даты завершения при изменении статуса с "completed"
     */
    protected static function booted()
    {
        static::updating(function ($order) {
            if ($order->isDirty('status') && $order->status === 'completed') {
                $order->completed_at = $order->completed_at ?? now();
            }

            if ($order->isDirty('status') && $order->getOriginal('status') === 'completed') {
                $order->completed_at = null;
            }
        });
    }
}
