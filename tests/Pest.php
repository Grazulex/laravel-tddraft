<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\LaravelTddraftServiceProvider;
use Orchestra\Testbench\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

// Configure the package for testing
uses()->beforeEach(function (): void {
    $this->app->register(LaravelTddraftServiceProvider::class);
})->in('Feature', 'Unit');
