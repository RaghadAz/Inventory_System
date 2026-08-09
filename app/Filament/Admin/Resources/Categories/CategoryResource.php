<?php

namespace App\Filament\Admin\Resources\Categories;

use App\Filament\Admin\Resources\Categories\Pages\CreateCategory;
use App\Filament\Admin\Resources\Categories\Pages\EditCategory;
use App\Filament\Admin\Resources\Categories\Pages\ListCategories;
use App\Filament\Admin\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Admin\Resources\Categories\Tables\CategoriesTable;
use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Categories';
    public static function form(Schema $schema): Schema
    {
        return $schema
                ->schema([
                    TextInput::make('name')
                        ->label('Category Name')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Description')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make('name')
                        ->label('Category Name')
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('description')
                        ->label('Description')
                        ->limit(50),

                    TextColumn::make('created_at')
                        ->label('Created At')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('products_count')
                        ->label('Total Remaining Stock')
                        ->getStateUsing(fn (Category $record): int => (int) $record->products()->sum('quantity'))
                        ->color('info')
                        ->weight('bold'),
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
