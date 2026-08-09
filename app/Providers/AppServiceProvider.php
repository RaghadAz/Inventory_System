<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Sale;
use App\Observers\SaleObserver; 

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Sale::observe(SaleObserver::class);
    }
}
