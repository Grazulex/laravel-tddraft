<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\InitCommand;
use Grazulex\LaravelTddraft\Console\Commands\MakeCommand;
use Grazulex\LaravelTddraft\Console\Commands\TestCommand;
use Grazulex\LaravelTddraft\LaravelTddraftServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

it('can test basic Laravel TDDraft package structure', function (): void {
    expect(class_exists(LaravelTddraftServiceProvider::class))->toBeTrue();
    expect(class_exists(InitCommand::class))->toBeTrue();
    expect(class_exists(MakeCommand::class))->toBeTrue();
    expect(class_exists(TestCommand::class))->toBeTrue();
});

it('verifies command stub files exist', function (): void {
    $stubsPath = __DIR__ . '/../../src/Console/Commands/stubs/';

    expect(File::exists($stubsPath . 'test-template.stub'))->toBeTrue();
    expect(File::exists($stubsPath . 'example-draft.stub'))->toBeTrue();
    expect(File::exists($stubsPath . 'phpunit-complete.stub'))->toBeTrue();
    expect(File::exists($stubsPath . 'pest-exclude-tddraft.stub'))->toBeTrue();
});

it('can read and validate config file', function (): void {
    $configPath = __DIR__ . '/../../src/Config/tddraft.php';

    expect(File::exists($configPath))->toBeTrue();

    $config = require $configPath;
    expect($config)->toBeArray();
});

it('validates service provider registration', function (): void {
    $provider = new LaravelTddraftServiceProvider(app());

    expect($provider)->toBeInstanceOf(ServiceProvider::class);
});
