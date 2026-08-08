<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('month')
                            ->label('Financial Report Month')
                            ->options([
                                '01' => '(1) January',
                                '02' => '(2) February',
                                '03' => '(3) March',
                                '04' => '(4) April',
                                '05' => '(5) May',
                                '06' => '(6) June',
                                '07' => '(7) July',
                                '08' => '(8) August',
                                '09' => '(9) September',
                                '10' => '(10) October',
                                '11' => '(11) November',
                                '12' => '(12) December',
                            ])

                            ->default(date('m'))
                            ->live(),

                        Select::make('year')
                            ->label('Year')
                            ->options([
                                '2025' => '2025',
                                '2026' => '2026',
                                '2027' => '2027',
                            ])
                            ->default(date('Y'))
                            ->live(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public static function canViewAny(): bool
    {
        return (string) auth()->user()?->getAttribute('role') === 'admin';
    }
}
