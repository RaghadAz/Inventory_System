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
            Stat::make('إجمالي حركة المخزن', StockMovement::count()),
            
            // تغيير change_type = increase إلى type = in
            Stat::make('حركات الإدخال (In)', StockMovement::where('type', 'in')->count())
                ->color('success'),

            // تغيير change_type = decrease إلى type = out
            Stat::make('حركات الإخراج (Out)', StockMovement::where('type', 'out')->count())
                ->color('danger'),
        ];
    }
}
