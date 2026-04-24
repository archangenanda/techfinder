<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // 1. AJOUTE CETTE LIGNE

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
    public function boot(): void // 2. MODIFIE CETTE FONCTION
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
    }
}
