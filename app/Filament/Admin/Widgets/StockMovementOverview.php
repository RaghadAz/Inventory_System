<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockMovementOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Total Stock Movements', StockMovement::count()),

            Stat::make('Inbound Movements (In)', StockMovement::where('type', 'in')->count())
                ->color('success'),

            Stat::make('Outbound Movements (Out)', StockMovement::where('type', 'out')->count())
                ->color('danger'),
        ];
    }
}
