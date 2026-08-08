<?php

namespace App\Filament\Admin\Pages;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Debt;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ScanProduct extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Sales Terminal (POS)  ';
    protected string $view = 'filament.admin.pages.scan-product';

    protected $listeners = ['barcode-scanned' => 'searchProduct'];

    public $barcode = '';
    public $searchTerm = '';
    public $lastScannedProduct = null;
    public $cart = [];
    public $totalPrice = 0;
    public $paymentType = 'cash';

    public function searchProduct($barcode)
    {
        $barcode = trim($barcode);

        if (empty($barcode)) {
            Notification::make()->title('Barcode field is empty ')->danger()->send();
            return;
        }

        $product = Product::where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->first();

        if (!$product) {
            Notification::make()->title(' Product not found : ' . $barcode)->danger()->send();
            return;
        }

        if ($product->quantity <= 0) {
            Notification::make()->title(' Product is out of stock : ' . $product->name)->warning()->send();
            return;
        }

        $this->addToCart($product);

        Notification::make()->title('Added to cart : ' . $product->name)->success()->send();

        $this->barcode = '';
    }

    public function manualSearch()
    {
        $searchTerm = trim($this->searchTerm);

        if (empty($searchTerm)) {
            Notification::make()->title('Search field is empty  ')->danger()->send();
            return;
        }

        $product = Product::where('barcode', $searchTerm)
            ->orWhere('sku', $searchTerm)
            ->orWhere('name', 'like', '%' . $searchTerm . '%')
            ->first();

        if (!$product) {
            Notification::make()->title('Product not found  : ' . $searchTerm)->danger()->send();
            return;
        }

        if ($product->quantity <= 0) {
            Notification::make()->title('Product is out of stock  : ' . $product->name)->warning()->send();
            return;
        }

        $this->addToCart($product);

        $this->searchTerm = '';
    }

    protected function addToCart(Product $product): void
    {
        $this->cart[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'cost_price' => $product->cost_price,
            'quantity' => 1,
            'total' => $product->price,
        ];

        $this->totalPrice = collect($this->cart)->sum('total');
        $this->lastScannedProduct = $product;
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            $this->totalPrice -= $this->cart[$index]['total'];
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if (!isset($this->cart[$index]) || $quantity < 1) {
            return;
        }

        $product = Product::find($this->cart[$index]['product_id']);
        if (!$product || $product->quantity < $quantity) {
            Notification::make()->title(' Requested quantity is not available ')->warning()->send();
            return;
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['total'] = $quantity * $this->cart[$index]['price'];

        $this->totalPrice = collect($this->cart)->sum('total');
    }

    public function completeSale()
    {
        $userId = auth()->id();

        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty ')->warning()->send();
            return;
        }

        $totalPrice = $this->totalPrice;
        $totalProfit = collect($this->cart)->sum(
            fn($item) => ($item['price'] - $item['cost_price']) * $item['quantity']
        );

        $sale = Sale::create([
            'user_id' => $userId,
            'customer_name' => $this->paymentType === 'debt' ? 'Credit Customer ' : 'Walk‑in Customer ',
            'payment_type' => $this->paymentType,
            'total_price' => $totalPrice,
            'total_profit' => $totalProfit,
            'paid_amount' => $this->paymentType === 'cash' ? $totalPrice : 0,
            'paid_amount_display' => $this->paymentType === 'cash' ? $totalPrice : 0,
            'remaining_price' => $this->paymentType === 'cash' ? 0 : $totalPrice,
        ]);

        foreach ($this->cart as $item) {

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'cost_price' => $item['cost_price'],
                'line_profit' => ($item['price'] - $item['cost_price']) * $item['quantity'],
                'total' => $item['total'],
            ]);

            Product::find($item['product_id'])->decrement('quantity', $item['quantity']);

            StockMovement::create([
                'product_id' => $item['product_id'],
                'user_id' => $userId,
                'change_type' => 'decrease',
                'amount' => $item['quantity'],
                'date' => now(),
            ]);
        }

        if ($this->paymentType === 'debt' && $sale->remaining_price > 0) {
            Debt::create([
                'person_name' => $sale->customer_name,
                'type' => 'customer',
                'amount' => $sale->remaining_price,
                'reason' => '  POS Invoice : ' . $sale->id,
                'sale_id' => $sale->id,
                'is_paid' => false,
                'notes' => 'Auto‑generated POS credit entry',
            ]);
        }

        Notification::make()->title('Sale completed successfully! ' . $sale->id)->success()->send();

        $this->resetScanner();
    }

    public function resetScanner()
    {
        $this->lastScannedProduct = null;
        $this->barcode = '';
        $this->searchTerm = '';
        $this->cart = [];
        $this->totalPrice = 0;
        $this->paymentType = 'cash';

        $this->dispatch('refresh-page');
    }
}
