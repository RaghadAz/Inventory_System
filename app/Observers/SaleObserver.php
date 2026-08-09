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
            }
        }

        if ($sale->payment_type === 'debt' && $sale->remaining_price > 0) {
            Debt::create([
                'sale_id'     => $sale->id,
                'user_id'     => auth()->id() ?? $sale->user_id ?? 1, 
                'person_name' => $sale->customer_name,
                'type'        => 'customer',
                'amount'      => $sale->remaining_price,
                'reason'      => 'Sales Invoice #' . $sale->id,
                'is_paid'     => 0,
                'notes'       => 'Auto-Generated Debt from Sales Invoice',
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
            }
        }

        $sale->debts()->delete();
    }
}
