<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_id', 'quantity',
        'price', 'cost_price', 'line_profit', 'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'line_profit' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected $attributes = [
        'total' => 0,
        'cost_price' => 0,
        'line_profit' => 0,
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    

    protected static function booted(): void
    {
        static::creating(function ($item) {
            $item->total = $item->quantity * $item->price;
        });
        
        static::updating(function ($item) {
            $item->total = $item->quantity * $item->price;
        });
    }
}