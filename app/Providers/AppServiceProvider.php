<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Sale;
use App\Observers\SaleObserver;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (env('APP_ENV') === 'production')
            URL::forceScheme('https');

        Livewire::setScriptRoute(function ($handle) {
        return URL::to('/vendor/livewire/livewire.js', $handle);});
        Sale::observe(SaleObserver::class);
    }
}
