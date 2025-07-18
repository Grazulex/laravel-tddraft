<?php

declare(strict_types=1);

namespace Tests\Unit;

use Grazulex\LaravelTddraft\LaravelTddraftServiceProvider;
use Orchestra\Testbench\TestCase;

class BasicTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelTddraftServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup the application environment for testing
        $app['config']->set('tddraft.enabled', true);
    }

    public function test_service_provider_is_loaded(): void
    {
        $this->assertNotNull($this->app);
        // Test that the service provider is registered instead of bound
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey(LaravelTddraftServiceProvider::class, $providers);
    }

    public function test_config_is_published(): void
    {
        $this->assertTrue(config('tddraft.enabled'));
    }

    public function test_package_configuration_is_available(): void
    {
        $this->assertNotNull($this->app);
        // Test that config is properly merged
        $this->assertIsArray(config('tddraft'));
        $this->assertArrayHasKey('enabled', config('tddraft'));
        $this->assertArrayHasKey('defaults', config('tddraft'));
        $this->assertArrayHasKey('cache', config('tddraft'));
        $this->assertArrayHasKey('logging', config('tddraft'));
    }
}
