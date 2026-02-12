<?php

namespace Redeemly\CatalogueIntegration\Providers;

use Illuminate\Support\ServiceProvider;
use Redeemly\CatalogueIntegration\Services\CatalogueService;
use Redeemly\CatalogueIntegration\Facades\Catalogue;
use Illuminate\Contracts\Foundation\Application;

class CatalogueIntegrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../Config/catalogue.php',
            'catalogue'
        );

        // Register the catalogue service as singleton
        $this->app->singleton(CatalogueService::class, function (Application $app) {
            return new CatalogueService(
                config('catalogue.base_url'),
                config('catalogue.credentials'),
                config('catalogue.http', []),
                $app['cache']
            );
        });

        // Register the facade
        $this->app->bind('catalogue', CatalogueService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../Config/catalogue.php' => config_path('catalogue.php'),
            ], 'catalogue-config');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/catalogue-integration'),
            ], 'catalogue-views');
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'catalogue-integration');

        // Register routes (available for testing in all environments)
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            CatalogueService::class,
            'catalogue',
        ];
    }
}
