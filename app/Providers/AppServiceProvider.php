<?php

namespace App\Providers;

use App\Models\Budget;
use App\Models\NetWorth;
use App\Observers\BudgetObserver;
use App\Observers\NetWorthObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the NetWorthObserver
        NetWorth::observe(NetWorthObserver::class);

    }
}
