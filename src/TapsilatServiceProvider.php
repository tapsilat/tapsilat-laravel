<?php

namespace Tapsilat\Laravel;

use Illuminate\Support\ServiceProvider;
use Tapsilat\TapsilatAPI;

class TapsilatServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tapsilat.php',
            'tapsilat'
        );

        $this->app->singleton(TapsilatManager::class, function ($app) {
            return new TapsilatManager($app['config']['tapsilat']);
        });

        $this->app->singleton(TapsilatAPI::class, function ($app) {
            return $app->make(TapsilatManager::class)->client();
        });

        $this->app->alias(TapsilatManager::class, 'tapsilat');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tapsilat.php' => config_path('tapsilat.php'),
            ], 'tapsilat-config');

            $this->commands([
                Console\HealthCheckCommand::class,
                Console\InstallCommand::class,
            ]);
        }

        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        if ($this->app['config']['tapsilat.webhook_secret']) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/webhooks.php');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            TapsilatManager::class,
            TapsilatAPI::class,
            'tapsilat',
        ];
    }
}
