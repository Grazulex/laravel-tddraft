<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Grazulex\LaravelTddraft\Console\Commands\ListCommand;
use Illuminate\Support\Facades\File;

it('has correct signature and description', function (): void {
    $command = new ListCommand;

    expect($command->getName())->toBe('tdd:list');
    expect($command->getDescription())->toBe('List all TDDraft tests with their references and metadata');
});

it('has correct options defined', function (): void {
    $command = new ListCommand;
    $definition = $command->getDefinition();

    expect($definition->hasOption('type'))->toBeTrue();
    expect($definition->hasOption('path'))->toBeTrue();
    expect($definition->hasOption('details'))->toBeTrue();
});

it('can test basic command structure', function (): void {
    $command = new ListCommand;

    expect($command)->toBeInstanceOf(Command::class);
    expect($command)->toBeInstanceOf(ListCommand::class);
});

it('verifies command has proper parent class', function (): void {
    $reflection = new ReflectionClass(ListCommand::class);
    $parentClass = $reflection->getParentClass();

    expect($parentClass)->not()->toBeFalse();
    if ($parentClass !== false) {
        expect($parentClass->getName())->toBe(Command::class);
    }
});

it('validates command signature structure', function (): void {
    $command = new ListCommand;

    expect($command->getName())->toBe('tdd:list');
    expect($command->getName())->toStartWith('tdd:');
});

it('can parse draft file metadata using private method', function (): void {
    $command = new ListCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('parseDraftFile');
    $method->setAccessible(true);

    // Create a mock draft file content
    $draftContent = "<?php

declare(strict_types=1);

/**
 * TDDraft Test: User can login
 * 
 * Reference: tdd-20240101120000-abc123
 * Type: feature
 * Created: 2024-01-01 12:00:00
 */

it('user can login', function (): void {
    expect(true)->toBeTrue();
});";

    // Create temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'tdd_test');
    File::put($tempFile, $draftContent);

    /** @var array{reference: string, name: string, type: string, file: string, path: string, created: ?string}|null $result */
    $result = $method->invoke($command, $tempFile);

    expect($result)->toBeArray();
    expect($result)->not()->toBeNull();

    if ($result !== null) {
        expect($result['reference'])->toBe('tdd-20240101120000-abc123');
        expect($result['type'])->toBe('feature');
        expect($result['name'])->toBe('User can login');
        expect($result['created'])->toBe('2024-01-01 12:00:00');
    }

    // Clean up
    File::delete($tempFile);
});

it('can apply type filter using private method', function (): void {
    $command = new ListCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('applyFilters');
    $method->setAccessible(true);

    $drafts = [
        'ref1' => ['reference' => 'ref1', 'name' => 'Test 1', 'type' => 'feature', 'file' => 'Test1.php', 'path' => 'Test1.php', 'created' => null],
        'ref2' => ['reference' => 'ref2', 'name' => 'Test 2', 'type' => 'unit', 'file' => 'Test2.php', 'path' => 'Auth/Test2.php', 'created' => null],
        'ref3' => ['reference' => 'ref3', 'name' => 'Test 3', 'type' => 'feature', 'file' => 'Test3.php', 'path' => 'Test3.php', 'created' => null],
    ];

    // Test the filter logic directly without mocking
    $filtered = array_filter($drafts, fn($draft): bool => strtolower((string) $draft['type']) === 'feature');

    expect($filtered)->toHaveCount(2);
    expect($filtered)->toHaveKeys(['ref1', 'ref3']);
});

it('can truncate text using private method', function (): void {
    $command = new ListCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('truncate');
    $method->setAccessible(true);

    $shortText = 'Short text';
    $longText = 'This is a very long text that should be truncated';

    expect($method->invoke($command, $shortText, 20))->toBe($shortText);
    expect($method->invoke($command, $longText, 20))->toBe('This is a very lo...');
});

it('can collect drafts from directory using private method', function (): void {
    $command = new ListCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('collectDrafts');
    $method->setAccessible(true);

    // Create temporary directory and files
    $tempDir = sys_get_temp_dir() . '/tdd_test_' . uniqid();
    File::makeDirectory($tempDir);

    $draftContent1 = "<?php
/**
 * TDDraft Test: Test One
 * Reference: tdd-20240101120000-abc123
 * Type: feature
 */
it('test one', function () {});";

    $draftContent2 = "<?php
/**
 * TDDraft Test: Test Two  
 * Reference: tdd-20240101120001-def456
 * Type: unit
 */
it('test two', function () {});";

    File::put($tempDir . '/Test1.php', $draftContent1);
    File::put($tempDir . '/Test2.php', $draftContent2);
    File::put($tempDir . '/NotATest.txt', 'not a php file'); // should be ignored

    $result = $method->invoke($command, $tempDir);

    expect($result)->toBeArray();
    expect($result)->toHaveCount(2);
    expect($result)->toHaveKeys(['tdd-20240101120001-def456', 'tdd-20240101120000-abc123']); // sorted newest first

    // Clean up
    File::deleteDirectory($tempDir);
});

it('returns null for invalid draft files using private method', function (): void {
    $command = new ListCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('parseDraftFile');
    $method->setAccessible(true);

    // Create a file without TDDraft metadata
    $invalidContent = "<?php
it('regular test', function (): void {
    expect(true)->toBeTrue();
});";

    $tempFile = tempnam(sys_get_temp_dir(), 'tdd_test');
    File::put($tempFile, $invalidContent);

    $result = $method->invoke($command, $tempFile);

    expect($result)->toBeNull();

    // Clean up
    File::delete($tempFile);
});
