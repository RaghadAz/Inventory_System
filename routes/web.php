<?php

use App\Http\Controllers\SalePdfController;
use App\Models\Sale;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');
Route::get('/admin/sales/{record}/print', [SalePdfController::class, 'print'])
    ->name('sales.print');

Route::get('/invoice/{sale}', function (Sale $sale) {
    $sale->load('saleItems.product');
    return view('invoices.single', compact('sale'));
})->name('sales.invoice');

Route::get('/bulk-invoice/{ids}', function ($ids) {
    $saleIds = explode(',', $ids);
    $sales = Sale::whereIn('id', $saleIds)->get();
    return view('invoices.bulk', compact('sales'));
})->name('sales.bulk.invoice');


Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/settings', function () {
        return view('admin.settings');
    });
});

Route::middleware(['auth', 'role:cashier'])->group(function () {});
Route::delete('/invoices/{id}', function ($id) {
    $sale = \App\Models\Sale::findOrFail($id);

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
})->middleware(['web', 'auth'])->name('invoices.destroy');
