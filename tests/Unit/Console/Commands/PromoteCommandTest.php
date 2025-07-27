<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\PromoteCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

it('has correct signature and description', function (): void {
    $command = new PromoteCommand;

    expect($command->getName())->toBe('tdd:promote');
    expect($command->getDescription())->toBe('Promote a TDDraft test to the CI test suite (tests/Feature or tests/Unit)');
});

it('has correct options defined', function (): void {
    $command = new PromoteCommand;
    $definition = $command->getDefinition();

    expect($definition->hasOption('target'))->toBeTrue();
    expect($definition->hasOption('file'))->toBeTrue();
    expect($definition->hasOption('new-file'))->toBeTrue();
    expect($definition->hasOption('class'))->toBeTrue();
    expect($definition->hasOption('force'))->toBeTrue();
    expect($definition->hasOption('keep-draft'))->toBeTrue();

    expect($definition->hasArgument('reference'))->toBeTrue();
});

it('can read promoted test stub', function (): void {
    $stubPath = __DIR__ . '/../../../../src/Console/Commands/stubs/promoted-test.stub';

    expect(File::exists($stubPath))->toBeTrue();

    $content = File::get($stubPath);
    expect($content)->toContain('{{originalReference}}');
    expect($content)->toContain('{{originalName}}');
    expect($content)->toContain('{{promotedDate}}');
    expect($content)->toContain('{{testContent}}');
    expect($content)->toContain('Promoted from TDDraft');
});

it('can parse draft file content using private method', function (): void {
    $command = new PromoteCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('parseDraftFile');
    $method->setAccessible(true);

    // Create a mock draft file content
    $draftContent = "/**
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

    /** @var array<string, string> $result */
    $result = $method->invoke($command, $tempFile);

    expect($result)->toBeArray();
    expect($result['reference'])->toBe('tdd-20240101120000-abc123');
    expect($result['type'])->toBe('feature');
    expect($result['name'])->toBe('User can login');
    expect($result['test_content'])->toContain("it('user can login'");

    // Clean up
    File::delete($tempFile);
});

it('can determine target directory using private method', function (): void {
    $command = new PromoteCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('determineTargetDirectory');
    $method->setAccessible(true);

    // Test with explicit target
    expect($method->invoke($command, 'feature', null))->toBe('Feature');
    expect($method->invoke($command, 'unit', null))->toBe('Unit');
    expect($method->invoke($command, 'FEATURE', null))->toBe('Feature');

    // Test with test type fallback
    expect($method->invoke($command, null, 'feature'))->toBe('Feature');
    expect($method->invoke($command, null, 'unit'))->toBe('Unit');

    // Test default
    expect($method->invoke($command, null, null))->toBe('Feature');
});

it('can clean test content using private method', function (): void {
    $command = new PromoteCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('cleanTestContent');
    $method->setAccessible(true);

    $draftContent = "it('user can login', function (): void {
    // TODO: Implement your test scenario here

    // This test starts in the \"red\" phase - it should fail initially
    // 1. Write the test (red phase)
    // 2. Make it pass (green phase) 
    // 3. Promote it to your CI test suite (tests/Feature/)

    expect(true)->toBeTrue('Replace this with your actual test implementation');
    expect(\$user->login())->toBeTrue();
});";

    $cleaned = $method->invoke($command, $draftContent);

    expect($cleaned)->toContain("it('user can login'");
    expect($cleaned)->toContain('expect($user->login())->toBeTrue()');
    expect($cleaned)->not()->toContain('TODO: Implement your test');
    expect($cleaned)->not()->toContain('This test starts in the');
    expect($cleaned)->not()->toContain('1. Write the test');
    expect($cleaned)->not()->toContain("expect(true)->toBeTrue('Replace this");
});

it('can generate promoted test content using private method', function (): void {
    $command = new PromoteCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('generatePromotedTestContent');
    $method->setAccessible(true);

    $testInfo = [
        'reference' => 'tdd-20240101120000-abc123',
        'type' => 'feature',
        'name' => 'User can login',
        'test_content' => "it('user can login', function (): void {\n    expect(\$user->login())->toBeTrue();\n});",
    ];

    $content = $method->invoke($command, $testInfo);

    expect($content)->not->toContain('class UserLoginTest extends TestCase');
    expect($content)->toContain('Original Reference: tdd-20240101120000-abc123');
    expect($content)->toContain('Original Name: User can login');
    expect($content)->toContain("it('user can login'");
    expect($content)->toContain('Promoted from TDDraft');
});

it('validates command signature structure', function (): void {
    $command = new PromoteCommand;

    expect($command->getName())->toBe('tdd:promote');
    expect($command->getName())->toStartWith('tdd:');
});

it('can test basic command structure', function (): void {
    $command = new PromoteCommand;

    expect($command)->toBeInstanceOf(Command::class);
    expect($command)->toBeInstanceOf(PromoteCommand::class);
});

it('verifies command has proper parent class', function (): void {
    $reflection = new ReflectionClass(PromoteCommand::class);
    $parentClass = $reflection->getParentClass();

    expect($parentClass)->not()->toBeFalse();
    if ($parentClass !== false) {
        expect($parentClass->getName())->toBe(Command::class);
    }
});
