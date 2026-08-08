<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Http\Controllers\ForecastController;
use BackedEnum;

class Forecast extends Page
{

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'AI Forecast';
    protected string $view = 'filament.admin.pages.forecast';


    public array $data = [];
    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function mount()
    {
        $controller = new ForecastController();
        $data = $controller->index()->getData();

        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }
}
