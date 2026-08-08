<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name',      'sku',  'barcode',   'cost_price',   'price',  'quantity',   'supplier_id',  'category_id',  'image',];
    protected $casts = [
        'cost_price' => 'decimal:2',
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];
    public function getStockAttribute(): int
    {
        return (int) $this->quantity;
    }
    public function getPurchasePriceAttribute(): float
    {
        return (float) ($this->cost_price ?? 0);
    }

    public function getSellingPriceAttribute(): float
    {
        return (float) ($this->price ?? 0);
    }
    public function getStockLabelAttribute(): string
    {
        if ($this->quantity <= 0) return "🚫 {$this->name} (Out of Stock )";
        if ($this->quantity <= 5) return "⚠️ {$this->name} (Remaining  {$this->quantity})";
        return "{$this->name} ({$this->quantity})";
    }
    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'PROD' . strtoupper(substr(uniqid(), -5));
            }
            if (empty($product->barcode)) {
                $product->barcode = $product->sku;
            }
        });

        static::updated(function ($product) {
            $oldQuantity = $product->getOriginal('quantity');
            $newQuantity = $product->quantity;

            if ($newQuantity < $oldQuantity && $newQuantity <= 5 && $product->supplier_id) {
                $supplier = $product->supplier;

                if ($supplier) {
                    $quantityNeeded = 5;
                    $amount = (float) (($product->cost_price ?? 0) * $quantityNeeded);

                    \App\Models\Debt::create([
                        'person_name' => $supplier->name,
                        'amount' => $amount,
                        'type' => 'supplier',
                        'is_paid' => 0,
                        'user_id' => auth()->id(),
                        'due_date' => now()->addDays(7),
                        'notes' => 'Auto‑Generated Stock Replenishment Request: ' . $product->name,
                    ]);

                    Notification::make()

                        ->title('⚠️ Low Stock Alert')
                        ->body("Product: {$product->name} – Supplier: {$supplier->name}")->warning()
                        ->send();
                }
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
