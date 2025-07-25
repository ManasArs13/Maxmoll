<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = ['warehouse_id', 'product_id'];

    public $timestamps = false;

    protected $fillable = ['product_id', 'warehouse_id', 'stock'];

    protected $casts = [
        'stock' => 'integer',
        'warehouse_id' => 'integer',
        'product_id' => 'integer'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
