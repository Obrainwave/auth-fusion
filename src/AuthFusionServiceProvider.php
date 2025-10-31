<?php

namespace Obrainwave\AuthFusion;

use Obrainwave\AuthFusion\Console\InstallDriverCommand;
use Obrainwave\AuthFusion\Manager\AuthDriverManager;
use Illuminate\Support\ServiceProvider;

class AuthFusionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/auth-fusion.php', 'auth-fusion');

        // Register the manager as singleton
        $this->app->singleton('auth-fusion', function ($app) {
            return new AuthDriverManager(
                $app['auth'],
                $app['request'],
                $app['config']['auth-fusion.driver']
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/auth-fusion.php' => config_path('auth-fusion.php'),
        ], 'auth-fusion-config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallDriverCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['auth-fusion'];
    }
}

