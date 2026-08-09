<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Debt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = today();

        $todaySales = (float) Sale::whereDate('created_at', $today)
            ->sum('total_price');

        \Log::info('StatsOverview', [
            'today' => $today->format('Y-m-d'),
            'sales' => $todaySales,
        ]);

        if ($todaySales == 0) {
            $todaySales = (float) Sale::whereBetween('created_at', [
                $today->startOfDay(),
                $today->endOfDay()
            ])->sum('total_price');

            \Log::info('StatsOverview whereBetween', ['sales' => $todaySales]);
        }

        $todayPaid = (float) Sale::whereDate('created_at', $today)
            ->sum('paid_amount');

        $todayExpenses = (float) Expense::whereDate('created_at', $today)
            ->sum('amount');

        $totalDebts = (float) Debt::where('is_paid', false)
            ->sum('amount');

        return [
            Stat::make('Todays Sales', number_format($todaySales, 0) . ' SYP')
                ->description('Collected Amount: ' . number_format($todayPaid, 0) . ' SYP')
                ->color($todaySales > 0 ? 'success' : 'gray'),

            Stat::make('Todays Expenses', number_format($todayExpenses, 0) . ' SYP')
                ->description('Current Month')
                ->color('danger'),

            Stat::make('Outstanding Debts', number_format($totalDebts, 0) . ' SYP')
                ->description('Unpaid')
                ->color('warning'),
        ];
    }
}
