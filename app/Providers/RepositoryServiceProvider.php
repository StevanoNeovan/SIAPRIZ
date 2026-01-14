<?php
// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Contracts\PenjualanRepositoryInterface;
use App\Repositories\Eloquent\PenjualanRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            DashboardRepositoryInterface::class,
            DashboardRepository::class
        );
        
        $this->app->bind(
            PenjualanRepositoryInterface::class,
            PenjualanRepository::class
        );
        
        // Add more bindings here...
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}