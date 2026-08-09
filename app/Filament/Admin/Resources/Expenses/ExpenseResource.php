<?php

namespace App\Filament\Admin\Resources\Expenses;

use App\Filament\Admin\Resources\Expenses\Pages\CreateExpense;
use App\Filament\Admin\Resources\Expenses\Pages\EditExpense;
use App\Filament\Admin\Resources\Expenses\Pages\ListExpenses;
use App\Models\Expense;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $recordTitleAttribute = 'reason';
    public static function canViewAny(): bool
    {
        return (string) auth()->user()?->getAttribute('role') === 'admin';
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            TextInput::make('title')
            ->label('Reason / Title')
            ->required()
            ->dehydrated(true)
            ->maxLength(255),

            TextInput::make('amount')
                ->numeric()
                ->required()
                ->label('Amount'),

            DatePicker::make('spent_at')
                ->default(now())
                ->required()
                ->label('Spent At'),

           // Textarea::make('notes')
                //->label('Notes')
               // ->columnSpanFull(),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title')->label('Reason / Title')->searchable(),
            TextColumn::make('amount')->money('SYP')->label('Amount'),
            TextColumn::make('spent_at')->date()->label('Spent At'),
        ]);
}

public static function getRelations(): array
{
    return [];
}

public static function getPages(): array
{
    return [
        'index' => ListExpenses::route('/'),
        'create' => CreateExpense::route('/create'),
        'edit' => EditExpense::route('/{record}/edit'),
    ];
}
}
