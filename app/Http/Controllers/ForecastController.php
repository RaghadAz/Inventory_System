<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    /**
     * عرض صفحة التنبؤات والتحليل
     */
    public function index(?Request $request = null)
    {
        // استخدام Request الممرر أو جلب الطلب الحالي تلقائياً لتجنب خطأ الـ Arguments
        $request = $request ?? request();

        // 1. إمكانية اختيار منتج معين أو جلب أول منتج كافتراضي
        $productId = $request->get('product_id');
        $product = $productId ? Product::find($productId) : Product::first();
        $allProducts = Product::select('id', 'name')->get();

        // 2. حماية النظام في حال عدم وجود أي منتجات في قاعدة البيانات
        if (!$product) {
            return view('filament.admin.pages.forecast', [
                'allProducts' => $allProducts,
                'selectedProduct' => null,
                'avg' => 0,
                'stock' => 0,
                'days' => 0,
                'alert' => 'لا توجد منتجات مضافة في قاعدة البيانات!',
                'status' => 'No Data',
                'risk' => 'None',
                'recommendedOrder' => 0,
                'trend' => '➖ Stable',
                'salesData' => [],
                'lastUpdated' => now()->format('d/m/Y H:i'),
                'prediction' => 0,
                'aiMessage' => 'No Prediction Available',
                'product_name' => 'غير محدد',
            ]);
        }

        // 3. جلب بيانات المبيعات الخاصة بالمنتج المحدد بأمان
        $sales = SaleItem::where('product_id', $product->id)->pluck('quantity');
        $salesData = $sales->toArray();

        // 4. استدعاء ملف الذكاء الاصطناعي لتوقع المبيعات
        $pythonFile = base_path('ai/forecast_ai.py');
        $command = 'python "' . $pythonFile . '" ' . implode(' ', $salesData);

        $result = @shell_exec($command);
        $aiResult = json_decode($result, true);

        $prediction = $aiResult['prediction'] ?? 0;
        $aiAverage = $aiResult['average'] ?? ($sales->count() > 0 ? $sales->avg() : 0);
        $aiMessage = $aiResult['message'] ?? 'No Prediction';

        // 5. حساب المواعيد والتحليلات بناءً على متوسط الاستهلاك
        $average = $aiAverage;
        $daysLeft = $average > 0 ? round($product->stock / $average) : 999;

        // تنبيهات حالة المخزون
        $alert = $product->stock < 10 ? 'Warning: Low Stock!' : null;

        // تحديد الحالة ومستوى المخاطرة والطلب الموصى به
        [$status, $risk, $recommendedOrder] = $this->calculateMetrics($product->stock, $daysLeft);

        // 6. حساب الاتجاه (Trend)
        $trend = '➖ Stable';
        if ($sales->count() >= 2) {
            $firstSale = $sales->first();
            $lastSale = $sales->last();

            if ($lastSale > $firstSale) {
                $trend = '📈 Increasing';
            } elseif ($lastSale < $firstSale) {
                $trend = '📉 Decreasing';
            }
        }

        $lastUpdated = now()->format('d/m/Y H:i');

        // 7. إرجاع البيانات للواجهة
        return view('filament.admin.pages.forecast', [
            'allProducts' => $allProducts,
            'selectedProduct' => $product,
            'avg' => round($average, 2),
            'stock' => $product->stock,
            'days' => $daysLeft > 365 ? '365+' : $daysLeft,
            'alert' => $alert,
            'status' => $status,
            'risk' => $risk,
            'recommendedOrder' => $recommendedOrder,
            'trend' => $trend,
            'salesData' => $salesData,
            'lastUpdated' => $lastUpdated,
            'prediction' => $prediction,
            'aiMessage' => $aiMessage,
            'product_name' => $product->name,
        ]);
    }

    /**
     * دالة مساعدة لحساب المؤشرات الرئيسية
     */
    private function calculateMetrics(int $stock, float $daysLeft): array
    {
        if ($stock <= 0) {
            return ['Out of Stock', 'High', 100];
        }

        if ($daysLeft <= 3) {
            return ['Out Of Stock Soon', 'High', 100];
        }

        if ($daysLeft <= 5) {
            return ['Running Low', 'High', 100];
        }

        if ($daysLeft <= 10) {
            return ['Running Low', 'Medium', 50];
        }

        return ['In Stock', 'Low', 20];
    }
}