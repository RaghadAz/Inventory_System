<?php

namespace App\Filament\Admin\Resources\Products;

use App\Filament\Admin\Resources\Products\Pages\CreateProduct;
use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Products';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Category'),

                Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->required()
                    ->label('Product Name '),

                TextInput::make('price')
                    ->label('Sale Price ')
                    ->numeric()
                    ->prefix('SYP')
                    ->required(),

                TextInput::make('cost_price')
                    ->label(' Cost Price')
                    ->numeric()
                    ->prefix('SYP')
                    ->required(),

                TextInput::make('sku')
                    ->label('Product Code')
                    ->nullable(),

                TextInput::make('barcode')
                    ->label(' Product Barcode')
                    ->autofocus()
                    ->placeholder('Scan product barcode here...')
                    ->default(fn() => 'PROD' . rand(100000, 999999))
                    ->unique(ignoreRecord: true)
                    ->required(),


                TextInput::make('quantity')
                    ->numeric()
                    ->label('Current Quantity ')
                    ->default(0)
                    ->minValue(0),

                FileUpload::make('image')
                    ->label('Product Image ')
                    ->image()
                    ->disk('assets_disk')
                    ->directory('')
                    ->visibility('public')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Product Name ')->searchable()->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Available Quantity ')
                    ->numeric()
                    ->sortable()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 3 => 'warning',
                        default => 'success',
                    })
                    ->icon(fn(int $state): string => match (true) {
                        $state <= 3 => 'heroicon-m-exclamation-triangle',
                        default => '',
                    })
                    ->description(fn(int $state): string => $state <= 3 ? 'Low Stock!' : ''),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('SYP', locale: 'ar_SY')
                    ->sortable(),

                ImageColumn::make('image')
                    ->label(' Product Image')
                    ->state(function ($record): string {
                        return asset('aseet/images/' . $record->image);
                    })
                    ->square(),

                ViewColumn::make('barcode')
                    ->label('Barcode')
                    ->view('filament.tables.columns.barcode-viewer'),


            ])
            ->searchPlaceholder('Search by name, supplier, or scan barcode ...')
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filter by Category'),
                SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
                    ->label('Filter by Supplier'),
            ]);
    }

    public static function canViewAny(): bool
    {
        return (string) auth()->user()?->getAttribute('role') === 'admin';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
