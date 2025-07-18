<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft;

use Grazulex\LaravelTddraft\Console\Commands\InitCommand;
use Grazulex\LaravelTddraft\Console\Commands\MakeCommand;
use Grazulex\LaravelTddraft\Console\Commands\TestCommand;
use Illuminate\Support\ServiceProvider;
use Override;
use Pest\Version;
use RuntimeException;

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
            ]);
        }

        if (class_exists('Pest\\Version') && defined('Pest\\Version::VERSION')) {
            /** @phpstan-ignore-next-line */
            $version = Version::VERSION;
            if (is_string($version) && version_compare($version, '3.0.0', '<')) {
                throw new RuntimeException("Laravel-TDDraft requires Pest 3.0 or higher. Current version: {$version}");
            }
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
