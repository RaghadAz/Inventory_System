<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Sale;
use App\Observers\SaleObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        if (!config('app.debug')) {
            ini_set('display_errors', '0');
        }
        Sale::observe(SaleObserver::class);
    }
}
