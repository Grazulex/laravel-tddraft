<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft;

use Override;
use Grazulex\LaravelTddraft\Console\Commands\InitCommand;
use Grazulex\LaravelTddraft\Console\Commands\ListCommand;
use Grazulex\LaravelTddraft\Console\Commands\MakeCommand;
use Grazulex\LaravelTddraft\Console\Commands\PromoteCommand;
use Grazulex\LaravelTddraft\Console\Commands\TestCommand;
use Illuminate\Support\ServiceProvider;

final class LaravelTddraftServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/Config/tddraft.php' => config_path('tddraft.php'),
        ], 'tddraft-config');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class,
                MakeCommand::class,
                TestCommand::class,
                PromoteCommand::class,
                ListCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/Config/tddraft.php',
            'tddraft'
        );
    }
}
