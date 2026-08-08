<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Symfony\Component\Process\Process;

class ForecastController extends Controller
{
    public function index()
    {
        $product = Product::first();
        if (!$product) {
            return view('filament.admin.pages.forecast', [
                'avg' => 0,
                'stock' => 0,
                'days' => 0,
                'alert' => "No products found",
                'status' => "No Data",
                'risk' => "N/A",
                'recommendedOrder' => 0,
                'trend' => "➖ No Trend",
                'salesData' => [],
                'lastUpdated' => now()->format('d/m/Y H:i'),
                'prediction' => 0,
                'aiMessage' => "No Prediction",
                'product_name' => "No Product",
            ]);
        }
        $sales =  \App\Models\SaleItem::where('product_id', $product->id)
            ->pluck('quantity');
        $salesData = $sales->toArray();

        $pythonFile = base_path('ai/forecast_ai.py');

        $command = "python \"$pythonFile\" " . implode(" ", $salesData);

        $result = shell_exec($command);

        $aiResult = json_decode($result, true);

        $prediction = $aiResult['prediction'] ?? 0;
        $aiAverage = $aiResult['average'] ?? 0;
        $aiMessage = $aiResult['message'] ?? "No Prediction";


        $average = $aiAverage;

        $daysLeft = 0;

        if ($average > 0) {
            $daysLeft = $product->stock / $average;
        }

        $alert = null;

        if ($product->stock < 10) {
            $alert = "Warning: Low Stock!";
        }
        $status = "In Stock";

        if ($product->stock < 10) {
            $status = "Running Low";
        }

        if ($daysLeft <= 3) {
            $status = "Out Of Stock Soon";
        }
        $risk = "Low";

        if ($daysLeft <= 10) {
            $risk = "Medium";
        }

        if ($daysLeft <= 5) {
            $risk = "High";
        }
        $recommendedOrder = 0;

        if ($daysLeft <= 5) {
            $recommendedOrder = 100;
        } elseif ($daysLeft <= 10) {
            $recommendedOrder = 50;
        } else {
            $recommendedOrder = 20;
        }


        $trend = "➖ Stable";

        if ($sales->count() >= 2) {

            $firstSale = $sales->first();
            $lastSale = $sales->last();

            if ($lastSale > $firstSale) {
                $trend = "📈 Increasing";
            } elseif ($lastSale < $firstSale) {
                $trend = "📉 Decreasing";
            }
        }
        $lastUpdated = now()->format('d/m/Y H:i');



        return view('filament.admin.pages.forecast', [
            'avg' => round($average, 2),
            'stock' => $product->stock,
            'days' => round($daysLeft),
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
}
