<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        // Register Livewire components
        Blade::component('medicine-table', \App\Http\Livewire\MedicineTable::class);
        Blade::component('pos-component', \App\Http\Livewire\PosComponent::class);
    }
}
