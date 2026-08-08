<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Tables\Actions\Action;

class LowStockProducts extends BaseWidget
{
    protected static ?string $heading = 'Products Nearing Stock Depletion (Less Than 5 Units)';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->where('quantity', '<=', 5)->orderBy('quantity', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Current Quantity')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->placeholder('Assigned Supplier'),
            ])
            ->actions([])
            ->bulkActions([])
            ->paginated(false);
    }
}
