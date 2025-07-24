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

    public $timestamps = false;

    protected $fillable = ['customer', 'created_at', 'completed_at', 'warehouse_id', 'status'];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('count');
    }

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
