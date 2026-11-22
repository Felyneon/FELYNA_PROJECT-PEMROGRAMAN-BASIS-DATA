<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\AksesKasir;
use Illuminate\Support\Facades\Blade;

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
        Blade::if('akses', fn (string $izin) => AksesKasir::bisa($izin));
    }
}
