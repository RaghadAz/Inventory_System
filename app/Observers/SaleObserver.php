<?php

namespace App\Observers;

use App\Models\Debt;
use App\Models\Product;
use App\Models\Sale;

class SaleObserver
{
    public function created(Sale $sale): void
    {
        foreach ($sale->saleItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->quantity -= $item->quantity;
                $product->save();
                \App\Models\StockMovement::create([
                    'product_id'  => $item->product_id,
                    'user_id'     => auth()->id(),
                    'change_type' => 'decrease',
                    'amount'      => $item->quantity,
                    'date'        => now(),
                    'quantity'    => -$item->quantity,
                    'type'        => 'sale',
                    'sale_id'     => $sale->id,
                    'notes'       => "Sale Invoice #{$sale->id}",
                ]);
            }
        }

        if ($sale->payment_type === 'debt' && $sale->remaining_price > 0) {
            Debt::create([
                'user_id' => auth()->id(),
                'sale_id' => $sale->id,
                'person_name' => $sale->customer_name,
                'type' => 'customer',
                'amount' => $sale->remaining_price,
                'reason' => "Sales Invoice #{$sale->id}",
                'is_paid' => false,
                'notes' => 'Auto‑Generated Debt from Sales Invoice',
            ]);
        }
    }

    public function deleted(Sale $sale): void
    {

        foreach ($sale->saleItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->quantity += $item->quantity;
                $product->save();

                \App\Models\StockMovement::create([
                    'product_id' => $item->product_id,
                    'quantity' => -$item->quantity,
                    'type' => 'sale_delete',
                    'amount'      => $item->quantity,
                    'change_type' => 'increase',
                    'sale_id' => $sale->id,
                    'notes' => 'Auto‑Generated Stock Deduction',
                    'user_id' => auth()->id(),
                    'date'        => now(),
                ]);
            }
        }

        $sale->debts()->delete();
    }
}
