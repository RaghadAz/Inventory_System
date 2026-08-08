<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Stock Movements ';
    protected static UnitEnum|string|null $navigationGroup = 'Inventory Management ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')
                ->label('Product')
                ->required(),

            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('User')
                ->required(),

            Forms\Components\Select::make('change_type')
                ->options([
                    'increase' => 'Increase',
                    'decrease' => 'Decrease',
                ])
                ->label('Movement Type

 ')
                ->required(),

            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->label('Quantity')
                ->required(),

            Forms\Components\DatePicker::make('date')
                ->label('Date')
                ->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('product.name')
                ->label('Product')
                ->searchable(),

            Tables\Columns\TextColumn::make('user.name')
                ->label('User'),

            Tables\Columns\BadgeColumn::make('change_type')
                ->label('Type')
                ->colors([
                    'success' => 'increase',
                    'danger' => 'decrease',
                ])
                ->formatStateUsing(fn($state) => $state === 'increase' ? 'Increase' : 'Decrease'),

            Tables\Columns\TextColumn::make('amount')
                ->label('Quantity'),

            Tables\Columns\TextColumn::make('date')
                ->label('Date')
                ->date(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At ')
                ->dateTime(),
        ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
        ];
    }
}
