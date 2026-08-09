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

    // معالجة البحث والجمع الصحيح
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
            Notification::make()->title('المنتج غير موجود: ' . $barcodeClean)->danger()->send();
            $this->barcode = '';
            return;
        }

        if ($product->quantity <= 0) {
            Notification::make()->title('المنتج نافد من المخزن: ' . $product->name)->warning()->send();
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
            // التحقق من توفر الكمية قبل زيادة العنصر
            if ($product->quantity < ($this->cart[$index]['quantity'] + 1)) {
                Notification::make()->title('الكمية المطلوبة غير متوفرة بالمخزن')->warning()->send();
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

        // حساب الإجمالي الصحيح بمجموع كافة عناصر السلة
        $this->totalPrice = collect($this->cart)->sum('total');
        $this->lastScannedProduct = $product;

        Notification::make()->title('تمت إضافة: ' . $product->name)->success()->send();
    }

    // إتمام البيع: الخصم المباشر وإنشاء الفاتورة
    public function completeSale()
    {
        if (empty($this->cart)) {
            Notification::make()->title('السلة فارغة')->warning()->send();
            return;
        }

        try {
            DB::transaction(function () {
                $totalProfit = collect($this->cart)->sum(
                    fn($item) => ($item['price'] - $item['cost_price']) * $item['quantity']
                );

                // 1. إنشاء الفاتورة في جدول sales
                $sale = Sale::create([
                    'user_id'         => auth()->id() ?? 1,
                    'customer_name'   => 'Walk-in Customer',
                    'payment_type'    => 'cash',
                    'total_price'     => $this->totalPrice,
                    'total_profit'    => $totalProfit,
                    'paid_amount'     => $this->totalPrice,
                    'remaining_price' => 0,
                ]);

                // 2. إنشاء عناصر الفاتورة والخصم من جدول المنتجات
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

                    // خصم الكمية المباشر من قاعدة البيانات
                    Product::where('id', $item['product_id'])->decrement('quantity', $item['quantity']);
                }

                Notification::make()
                    ->title('تم حفظ الفاتورة #' . $sale->id . ' وخصم الكميات بنجاح!')
                    ->success()
                    ->send();

                $this->resetScanner();
            });
        } catch (\Exception $e) {
            Notification::make()
                ->title('خطأ في عملية الحفظ')
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