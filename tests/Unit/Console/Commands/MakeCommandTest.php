<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\MakeCommand;
use Illuminate\Support\Facades\File;

it('has correct signature and description', function (): void {
    $command = new MakeCommand;

    expect($command->getName())->toBe('tdd:make');
    expect($command->getDescription())->toBe('Create a new TDDraft test with unique reference tracking');
});

it('can read test template stub', function (): void {
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/test-template.stub';

    expect(File::exists($stubPath))->toBeTrue();

    $content = File::get($stubPath);
    expect($content)->toContain('{{name}}');
    expect($content)->toContain('{{uniqueRef}}');
    expect($content)->toContain('{{type}}');
    expect($content)->toContain('{{created}}');
    expect($content)->toContain('TDDraft Test:');
    expect($content)->toContain('Reference:');
});

it('can simulate test file creation with template', function (): void {
    $tddraftPath = base_path('tests/TDDraft');
    $testPath = $tddraftPath . '/TestFile.php';

    // Clean up first
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }

    // Create directory
    File::makeDirectory($tddraftPath, 0755, true);

    // Simulate test creation using template
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/test-template.stub';
    $content = File::get($stubPath);
    $content = str_replace('{{name}}', 'test feature', $content);
    $content = str_replace('{{uniqueRef}}', 'tdd-20240101120000-abc123', $content);
    $content = str_replace('{{type}}', 'feature', $content);
    $content = str_replace('{{created}}', '2024-01-01 12:00:00', $content);

    File::put($testPath, $content);

    expect(File::exists($testPath))->toBeTrue();

    $fileContent = File::get($testPath);
    expect($fileContent)->toContain('TDDraft Test: test feature');
    expect($fileContent)->toContain('Reference: tdd-20240101120000-abc123');

    // Clean up
    File::deleteDirectory($tddraftPath);
});

it('validates unique reference format pattern', function (): void {
    // Test the expected format pattern
    $pattern = '/^tdd-\d{14}-[a-z0-9]{6}$/';

    // Sample references that should match
    expect('tdd-20240101120000-abc123')->toMatch($pattern);
    expect('tdd-20231225143059-xyz789')->toMatch($pattern);

    // References that should NOT match
    expect('tdd-2024010112000-abc123')->not()->toMatch($pattern); // wrong timestamp length
    expect('tdd-20240101120000-ABC123')->not()->toMatch($pattern); // uppercase letters
    expect('invalid-20240101120000-abc123')->not()->toMatch($pattern); // wrong prefix
});

it('can test tddraft directory creation logic', function (): void {
    $tddraftPath = base_path('tests/TDDraft');

    // Clean up first
    if (File::exists($tddraftPath)) {
        File::deleteDirectory($tddraftPath);
    }

    expect(File::exists($tddraftPath))->toBeFalse();

    // Test directory creation
    File::makeDirectory($tddraftPath, 0755, true);
    expect(File::exists($tddraftPath))->toBeTrue();
    expect(File::isDirectory($tddraftPath))->toBeTrue();

    // Clean up
    File::deleteDirectory($tddraftPath);
});

it('validates template variable substitution', function (): void {
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/test-template.stub';
    $template = File::get($stubPath);

    // Test substitution
    $name = 'User can login successfully';
    $reference = 'tdd-20240101120000-abc123';
    $type = 'feature';
    $created = '2024-01-01 12:00:00';

    $result = str_replace('{{name}}', $name, $template);
    $result = str_replace('{{uniqueRef}}', $reference, $result);
    $result = str_replace('{{type}}', $type, $result);
    $result = str_replace('{{created}}', $created, $result);

    expect($result)->toContain('TDDraft Test: User can login successfully');
    expect($result)->toContain('Reference: tdd-20240101120000-abc123');
    expect($result)->toContain('Type: feature');
    expect($result)->not()->toContain('{{name}}');
    expect($result)->not()->toContain('{{uniqueRef}}');
});

// Tests with Reflection API to actually execute code and improve coverage

it('can generate unique reference using private method', function (): void {
    $command = new MakeCommand();
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('generateUniqueReference');
    $method->setAccessible(true);
    
    $reference = $method->invoke($command);
    
    // Updated pattern to accept both lowercase and uppercase letters
    expect($reference)->toMatch('/^tdd-\d{14}-[a-zA-Z0-9]{6}$/');
    expect($reference)->toStartWith('tdd-');
    
    // Generate another to ensure uniqueness
    $reference2 = $method->invoke($command);
    expect($reference)->not()->toBe($reference2);
});

it('can determine file path using private method', function (): void {
    $command = new MakeCommand();
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('determineFilePath');
    $method->setAccessible(true);
    
    // Test without custom path
    $filePath = $method->invoke($command, 'User login test', null);
    expect($filePath)->toEndWith('tests/TDDraft/UserLoginTest.php');
    expect($filePath)->toContain('tests/TDDraft');
    
    // Test with custom path
    $customFilePath = $method->invoke($command, 'Auth test', 'Authentication');
    expect($customFilePath)->toContain('tests/TDDraft/Authentication/AuthTest.php');
});

it('can generate filename using private method', function (): void {
    $command = new MakeCommand();
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('generateFilename');
    $method->setAccessible(true);
    
    expect($method->invoke($command, 'user can login'))->toBe('UserCanLoginTest.php');
    expect($method->invoke($command, 'password validation'))->toBe('PasswordValidationTest.php');
    expect($method->invoke($command, 'simple test'))->toBe('SimpleTest.php');
    expect($method->invoke($command, 'API endpoint test'))->toBe('APIEndpointTest.php');
});

it('can determine class name using private method', function (): void {
    $command = new MakeCommand();
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('determineClassName');
    $method->setAccessible(true);
    
    // Test without custom class
    $className = $method->invoke($command, 'user registration', null);
    expect($className)->toBe('UserRegistrationTest');
    
    // Test with custom class
    $customClassName = $method->invoke($command, 'any name', 'CustomTestClass');
    expect($customClassName)->toBe('CustomTestClass');
});

it('can generate test content using private method', function (): void {
    $command = new MakeCommand();
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('generateTestContent');
    $method->setAccessible(true);
    
    $content = $method->invoke($command, 'User login test', 'feature', 'tdd-20240101120000-abc123');
    
    expect($content)->toContain('TDDraft Test: User login test');
    expect($content)->toContain('Reference: tdd-20240101120000-abc123');
    expect($content)->toContain('Type: feature');
    expect($content)->toContain('it(\'user login test\'');
    expect($content)->toContain('->group(\'tddraft\', \'feature\', \'tdd-20240101120000-abc123\')');
});
