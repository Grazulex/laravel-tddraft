<?php

declare(strict_types=1);

namespace Tests\Feature;

use Grazulex\LaravelTddraft\LaravelTddraftServiceProvider;
use Orchestra\Testbench\TestCase;

class IntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelTddraftServiceProvider::class,
        ];
    }

    public function test_integration_example(): void
    {
        // Add your integration tests here
        $this->assertNotNull($this->app);
    }
}
