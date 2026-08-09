<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use BackedEnum;

class ScanProduct extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Scan Product';

    protected string $view = 'filament.admin.pages.scan-product';

    public $barcode = '';
    public $cart = [];
    public $totalPrice = 0;
    public $lastScannedProduct = null;

    public function searchProduct($barcode)
    {
        $barcodeClean = trim((string)$barcode);

        if (empty($barcodeClean)) {
            return;
        }

        $product = Product::where('barcode', $barcodeClean)
            ->orWhere('sku', $barcodeClean)
            ->first();

        if (!$product) {
            Notification::make()->title('Product not found: ' . $barcodeClean)->danger()->send();
            $this->barcode = '';
            return;
        }

        if ($product->quantity <= 0) {
            Notification::make()->title('Out of stock: '  . $product->name)->warning()->send();
            $this->barcode = '';
            return;
        }

        $this->addToCart($product);
        $this->barcode = '';
    }

    protected function addToCart($product)
    {
        $index = collect($this->cart)->search(fn($item) => $item['product_id'] === $product->id);

        if ($index !== false) {
            if ($product->quantity < ($this->cart[$index]['quantity'] + 1)) {
                Notification::make()->title('Requested quantity not available ')->warning()->send();
                return;
            }
            $this->cart[$index]['quantity'] += 1;
            $this->cart[$index]['total'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float)$product->price,
                'cost_price' => (float)($product->cost_price ?? 0),
                'quantity'   => 1,
                'total'      => (float)$product->price,
            ];
        }

        $this->totalPrice = collect($this->cart)->sum('total');
        $this->lastScannedProduct = $product;

        Notification::make()->title('Added: '  . $product->name)->success()->send();
    }

    public function completeSale()
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty ')->warning()->send();
            return;
        }

        try {
            DB::transaction(function () {
                $totalProfit = collect($this->cart)->sum(
                    fn($item) => ($item['price'] - $item['cost_price']) * $item['quantity']
                );

                $sale = Sale::create([
                    'user_id'         => auth()->id() ?? 1,
                    'customer_name'   => 'Walk-in Customer',
                    'payment_type'    => 'cash',
                    'total_price'     => $this->totalPrice,
                    'total_profit'    => $totalProfit,
                    'paid_amount'     => $this->totalPrice,
                    'remaining_price' => 0,
                ]);

                foreach ($this->cart as $item) {
                    SaleItem::create([
                        'sale_id'     => $sale->id,
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'price'       => $item['price'],
                        'cost_price'  => $item['cost_price'],
                        'line_profit' => ($item['price'] - $item['cost_price']) * $item['quantity'],
                        'total'       => $item['total'],
                    ]);

                    Product::where('id', $item['product_id'])->decrement('quantity', $item['quantity']);
                }

                Notification::make()
                    ->title('Invoice #' . $sale->id . ' saved and stock updated!')
                    ->success()
                    ->send();

                $this->resetScanner();
            });
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error while saving')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetScanner()
    {
        $this->cart = [];
        $this->totalPrice = 0;
        $this->lastScannedProduct = null;
        $this->barcode = '';
    }
}
