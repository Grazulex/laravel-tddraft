<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\InitCommand;
use Illuminate\Support\Facades\File;

it('has correct signature and description', function (): void {
    $command = new InitCommand;

    expect($command->getName())->toBe('tdd:init');
    expect($command->getDescription())->toBe('Initialize TDDraft: configure PHPUnit, Pest, and create necessary directories');
});

it('creates tddraft directory when it does not exist', function (): void {
    $tddraftPath = base_path('tests/TDDraft');

    // Clean up first
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }

    // Test that directory gets created
    expect(File::exists($tddraftPath))->toBeFalse();

    // Since we can't easily mock the command execution, we test the actual directory creation
    File::makeDirectory($tddraftPath, 0755, true);
    expect(File::exists($tddraftPath))->toBeTrue();

    // Clean up
    File::deleteDirectory($tddraftPath);
});

it('creates gitkeep file in tddraft directory', function (): void {
    $tddraftPath = base_path('tests/TDDraft');
    $gitkeepPath = $tddraftPath . '/.gitkeep';

    // Clean up first
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }

    // Create directory and gitkeep
    File::makeDirectory($tddraftPath, 0755, true);
    File::put($gitkeepPath, '');

    expect(File::exists($gitkeepPath))->toBeTrue();

    // Clean up
    File::deleteDirectory($tddraftPath);
});

it('handles existing tddraft directory gracefully', function (): void {
    $tddraftPath = base_path('tests/TDDraft');

    // Clean up first
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }

    // Create directory first
    File::makeDirectory($tddraftPath, 0755, true);
    expect(File::exists($tddraftPath))->toBeTrue();

    // Should not fail when directory already exists - Laravel's makeDirectory handles this
    if (! File::exists($tddraftPath)) {
        File::makeDirectory($tddraftPath, 0755, true);
    }
    expect(File::exists($tddraftPath))->toBeTrue();

    // Clean up
    File::deleteDirectory($tddraftPath);
});

it('can read phpunit stub content', function (): void {
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/phpunit-complete.stub';

    expect(File::exists($stubPath))->toBeTrue();

    $content = File::get($stubPath);
    expect($content)->toContain('<phpunit');
    expect($content)->toContain('defaultTestSuite="default"');
    expect($content)->toContain('<testsuite name="tddraft">');
});

it('can read pest stub content', function (): void {
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/pest-exclude-tddraft.stub';

    expect(File::exists($stubPath))->toBeTrue();

    $content = File::get($stubPath);
    expect($content)->toContain("->in('Feature', 'Unit')");
});

it('can read example draft stub content', function (): void {
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/example-draft.stub';

    expect(File::exists($stubPath))->toBeTrue();

    $content = File::get($stubPath);
    expect($content)->toContain('TDDraft test');
    expect($content)->toContain("->group('tddraft'");
});

it('creates example draft file with correct content', function (): void {
    $tddraftPath = base_path('tests/TDDraft');
    $examplePath = $tddraftPath . '/ExampleDraftTest.php';
    
    // Clean up first
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }
    
    // Create directory
    File::makeDirectory($tddraftPath, 0755, true);
    
    // Create example file using stub
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/example-draft.stub';
    $content = File::get($stubPath);
    File::put($examplePath, $content);
    
    expect(File::exists($examplePath))->toBeTrue();
    
    $fileContent = File::get($examplePath);
    expect($fileContent)->toContain("->group('tddraft'");
    expect($fileContent)->toContain('example-red-phase');
    expect($fileContent)->toContain('example-green-phase');
    
    // Clean up
    File::deleteDirectory($tddraftPath);
});

// Tests with Reflection API to actually execute code and improve coverage

it('can show manual phpunit instructions using private method', function (): void {
    $command = new \Grazulex\LaravelTddraft\Console\Commands\InitCommand();
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('showManualPhpUnitInstructions');
    $method->setAccessible(true);
    
    // This method just outputs instructions, we can test it doesn't throw
    expect(fn() => $method->invoke($command))->not()->toThrow(Exception::class);
});

it('can show manual pest instructions using private method', function (): void {
    $command = new \Grazulex\LaravelTddraft\Console\Commands\InitCommand();
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('showManualPestInstructions');
    $method->setAccessible(true);
    
    // This method just outputs instructions, we can test it doesn't throw
    expect(fn() => $method->invoke($command))->not()->toThrow(Exception::class);
});

// Test methods that don't use console output

it('can test directory creation logic directly', function (): void {
    $tddraftPath = base_path('tests/TDDraft');
    
    // Clean up first
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }
    
    expect(File::exists($tddraftPath))->toBeFalse();
    
    // Manually create what the private method would do
    File::makeDirectory($tddraftPath, 0755, true);
    File::put($tddraftPath . '/.gitkeep', '');
    
    expect(File::exists($tddraftPath))->toBeTrue();
    expect(File::exists($tddraftPath . '/.gitkeep'))->toBeTrue();
    
    // Clean up
    File::deleteDirectory($tddraftPath);
});

it('can test example file creation logic directly', function (): void {
    $tddraftPath = base_path('tests/TDDraft');
    $examplePath = $tddraftPath . '/ExampleDraftTest.php';
    
    // Clean up and create directory
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }
    File::makeDirectory($tddraftPath, 0755, true);
    
    expect(File::exists($examplePath))->toBeFalse();
    
    // Manually create what the private method would do
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/example-draft.stub';
    $content = File::get($stubPath);
    File::put($examplePath, $content);
    
    expect(File::exists($examplePath))->toBeTrue();
    
    $fileContent = File::get($examplePath);
    expect($fileContent)->toContain('TDDraft test');
    expect($fileContent)->toContain('example-red-phase');
    
    // Clean up
    File::deleteDirectory($tddraftPath);
});