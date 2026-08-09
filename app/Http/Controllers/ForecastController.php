<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    /**
     */
    public function index(?Request $request = null)
    {
        $request = $request ?? request();

        $productId = $request->get('product_id');
        $product = $productId ? Product::find($productId) : Product::first();
        $allProducts = Product::select('id', 'name')->get();

        if (!$product) {
            return view('filament.admin.pages.forecast', [
                'allProducts' => $allProducts,
                'selectedProduct' => null,
                'avg' => 0,
                'stock' => 0,
                'days' => 0,
                'alert' => 'No products added in the database!',
                'status' => 'No Data',
                'risk' => 'None',
                'recommendedOrder' => 0,
                'trend' => '➖ Stable',
                'salesData' => [],
                'lastUpdated' => now()->format('d/m/Y H:i'),
                'prediction' => 0,
                'aiMessage' => 'No Prediction Available',
                'product_name' => 'Undefined',
            ]);
        }

        $sales = SaleItem::where('product_id', $product->id)->pluck('quantity');
        $salesData = $sales->toArray();

        $pythonFile = base_path('ai/forecast_ai.py');
        $command = 'python "' . $pythonFile . '" ' . implode(' ', $salesData);

        $result = @shell_exec($command);
        $aiResult = json_decode($result, true);

        $prediction = $aiResult['prediction'] ?? 0;
        $aiAverage = $aiResult['average'] ?? ($sales->count() > 0 ? $sales->avg() : 0);
        $aiMessage = $aiResult['message'] ?? 'No Prediction';

        $average = $aiAverage;
        $daysLeft = $average > 0 ? round($product->stock / $average) : 999;

        $alert = $product->stock < 10 ? 'Warning: Low Stock!' : null;

        [$status, $risk, $recommendedOrder] = $this->calculateMetrics($product->stock, $daysLeft);

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
