<?php

namespace App\Filament\Admin\Resources\Suppliers;

use App\Filament\Admin\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Admin\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Admin\Resources\Suppliers\Pages\ListSuppliers;
use App\Models\Supplier;
use BackedEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;


class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Suppliers';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label(' Suppliers Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(' Email')
                    ->email()
                    ->required(),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->numeric()
                    ->required()
                    ->minLength(10)
                    ->maxLength(10)
                    ->regex('/^09\d{8}$/'),
                Textarea::make('address')
                    ->label('Address')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Supplier Name '),
            TextColumn::make('phone')->label('Phone Number'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
