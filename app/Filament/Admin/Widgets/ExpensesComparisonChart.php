<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class ExpensesComparisonChart extends ChartWidget
{
    protected ?string $heading = 'Expenses Comparison Chart';

    protected function getData(): array
    {
        $expenses = \App\Models\Expense::where('spent_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(spent_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Daily Expenses',
                    'data' => $expenses->pluck('total'),
                    'borderColor' => '#ef4444',
                    'fill' => 'start',
                ],
            ],
            'labels' => $expenses->pluck('date'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
