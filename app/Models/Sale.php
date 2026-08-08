<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'payment_type',
        'total_price',
        'total_profit',
        'paid_amount',
        'paid_amount_display',
        'remaining_price',
        'status',
    ];
    protected $casts = [
        'total_price' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_amount_display' => 'decimal:2',
        'remaining_price' => 'decimal:2',
    ];

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(\App\Models\Debt::class, 'sale_id');
    }

    protected static function booted(): void
    {
        static::deleting(function ($sale) {
            $sale->debts()->delete();
            $sale->saleItems()->delete();
        });
    }
}