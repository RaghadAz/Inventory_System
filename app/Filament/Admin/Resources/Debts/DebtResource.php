<?php

namespace App\Filament\Admin\Resources\Debts;

use App\Filament\Admin\Resources\Debts\Pages\CreateDebt;
use App\Filament\Admin\Resources\Debts\Pages\EditDebt;
use App\Filament\Admin\Resources\Debts\Pages\ListDebts;
use App\Models\Debt;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = ' Debt Management';
    protected static ?string $pluralModelLabel = ' Debts & Liabilities';
    protected static ?string $modelLabel = 'Debt';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('person_name')
                    ->label('Person Name (Customer / Supplier) ')
                    ->required(),

                Select::make('type')
                    ->label('Category')
                    ->options([
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                    ])
                    ->required(),

                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->prefix('SYP')
                    ->required(),

                TextInput::make('reason')
                    ->label('Reason (e.g., Perfume Payment) '),

                DatePicker::make('due_date')
                    ->label('Expected Due Date '),

                Toggle::make('is_paid')
                    ->label('Fully Paid ')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('person_name')
                    ->label(' Party / Supplier / Customer')
                    ->getStateUsing(function ($record) {
                        if (!empty($record->person_name)) {
                            return $record->person_name;
                        }

                        if ($record->sale_id && $record->sale && !empty($record->sale->customer_name)) {
                            return $record->sale->customer_name;
                        }

                        if ($record->user) {
                            return $record->user->name;
                        }

                        if (str_contains($record->notes ?? '', 'Auto-Generated Request')) {
                            return 'Auto‑Generated Stock Deduction';
                        }

                        return 'Walk‑In Cash Customer ';
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Details')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Price')
                    ->money('SYP'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'supplier' => 'primary',
                        'customer' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'supplier' => 'Supplier',
                        'customer' => 'Customer',
                        default => $state,
                    }),

                TextColumn::make('due_date')
                    ->label('Due Date ')
                    ->date(),

                TextColumn::make('is_paid')
                    ->label('Status')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn(bool $state): string => $state ? '✅ Fully Paid' : '❌ Unpaid '),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Category')
                    ->options([
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                    ]),

                SelectFilter::make('is_paid')
                    ->label('Payment Status ')
                    ->options([
                        true => 'Paid ',
                        false => 'Unpaid ',
                    ]),
            ])
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDebts::route('/'),
            'create' => CreateDebt::route('/create'),
            'edit' => EditDebt::route('/{record}/edit'),
        ];
    }
}
