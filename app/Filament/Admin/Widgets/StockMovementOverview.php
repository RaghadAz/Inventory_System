<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockMovementOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()->role === 'admin';
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Total Stock Movements', StockMovement::count())
                ->description('All Inventory Movements')
                ->color('primary'),

            Stat::make('Stock Increases', StockMovement::where('change_type', 'increase')->count())
                ->description('Increase Transactions')
                ->color('success'),

            Stat::make('Stock Decreases', StockMovement::where('change_type', 'decrease')->count())
                ->description('Decrease Transactions')
                ->color('danger'),
        ];
    }
}
