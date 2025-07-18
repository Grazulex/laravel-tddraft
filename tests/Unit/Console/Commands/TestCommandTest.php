<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\TestCommand;
use Illuminate\Console\Command;

it('has correct signature and description', function (): void {
    $command = new TestCommand;

    expect($command->getName())->toBe('tdd:test');
    expect($command->getDescription())->toBe('Run TDDraft tests only (alias for pest --testsuite=tddraft)');
});

it('has correct options defined', function (): void {
    $command = new TestCommand;
    $definition = $command->getDefinition();

    expect($definition->hasOption('filter'))->toBeTrue();
    expect($definition->hasOption('coverage'))->toBeTrue();
    expect($definition->hasOption('parallel'))->toBeTrue();
    expect($definition->hasOption('stop-on-failure'))->toBeTrue();
});

it('can verify command basic structure', function (): void {
    $command = new TestCommand;

    expect($command->getName())->toBe('tdd:test');
    expect($command->getDescription())->toContain('TDDraft tests');
    expect($command->getDescription())->toContain('pest');
});

it('validates command options configuration', function (): void {
    $command = new TestCommand;
    $definition = $command->getDefinition();

    // Verify all expected options exist (based on actual command signature)
    $expectedOptions = ['filter', 'coverage', 'parallel', 'stop-on-failure'];

    foreach ($expectedOptions as $option) {
        expect($definition->hasOption($option))->toBeTrue("Option '{$option}' should be defined");
    }
});

it('can test basic command instantiation', function (): void {
    $command = new TestCommand;

    expect($command)->toBeInstanceOf(Command::class);
    expect($command)->toBeInstanceOf(TestCommand::class);
});

it('verifies command has proper parent class', function (): void {
    $reflection = new ReflectionClass(TestCommand::class);
    $parentClass = $reflection->getParentClass();

    expect($parentClass)->not()->toBeFalse();
    if ($parentClass !== false) {
        expect($parentClass->getName())->toBe(Command::class);
    }
});

it('can check command signature structure', function (): void {
    $command = new TestCommand;
    $signature = $command->getName();

    expect($signature)->toStartWith('tdd:');
    expect($signature)->toBe('tdd:test');
});

// Tests that actually execute some code logic

it('can verify options are properly parsed', function (): void {
    $command = new TestCommand;

    // Test that command initializes without throwing errors
    expect($command)->toBeInstanceOf(Command::class);

    // Test signature parsing - this will exercise Laravel's signature parsing code
    $definition = $command->getDefinition();

    // Test that all our expected options are properly defined
    $options = $definition->getOptions();
    $optionNames = array_keys($options);

    expect($optionNames)->toContain('filter');
    expect($optionNames)->toContain('coverage');
    expect($optionNames)->toContain('parallel');
    expect($optionNames)->toContain('stop-on-failure');
});

it('can test command construction and properties', function (): void {
    $command = new TestCommand;

    // Test that command properties are set correctly
    $reflection = new ReflectionClass($command);

    $signatureProperty = $reflection->getProperty('signature');
    $signatureProperty->setAccessible(true);
    $signature = $signatureProperty->getValue($command);

    expect($signature)->toContain('tdd:test');
    expect($signature)->toContain('--filter=');
    expect($signature)->toContain('--coverage');
    expect($signature)->toContain('--parallel');
    expect($signature)->toContain('--stop-on-failure');

    $descriptionProperty = $reflection->getProperty('description');
    $descriptionProperty->setAccessible(true);
    $description = $descriptionProperty->getValue($command);

    expect($description)->toBe('Run TDDraft tests only (alias for pest --testsuite=tddraft)');
});
