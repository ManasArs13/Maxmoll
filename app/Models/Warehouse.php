<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    /** @use HasFactory<\Database\Factories\WarehousFactory> */
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
        'name'
    ];

    /**
     * Отношение к товарам через промежуточную таблицу остатков
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stocks')
            ->withPivot('stock');
    }
}
