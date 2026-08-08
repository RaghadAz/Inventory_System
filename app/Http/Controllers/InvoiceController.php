<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class InvoiceController extends Controller
{
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);

        foreach ($sale->saleItems as $item) {
            $product = $item->product;
            if ($product) {
                $product->increment('quantity', $item->quantity);
            }
        }

        $sale->debts()->delete();
        $sale->saleItems()->delete();
        $sale->delete();

        return redirect()->route('filament.admin.resources.sales.index')->with('success', 'Invoice deleted successfully');
    }
}
