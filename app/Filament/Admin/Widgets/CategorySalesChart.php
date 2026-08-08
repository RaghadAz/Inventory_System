<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class CategorySalesChart extends ChartWidget
{
    protected ?string $heading = 'Category Sales Chart';

    protected function getData(): array
    {
        $salesData = \App\Models\SaleItem::query()
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('sum(sale_items.quantity * sale_items.price) as total, categories.name as category_name')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Sales by Category',
                    'data' => $salesData->pluck('total')->toArray(),
                    'backgroundColor' => '#818cf8',
                    'borderColor' => '#818cf8',
                ],
            ],
            'labels' => $salesData->pluck('category_name')->toArray(),
        ];
    }
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'animation' => true,
                ],
            ],
        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}
