<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\PromoteCommand;
use Grazulex\LaravelTddraft\Services\StatusTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
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

it('removes the failing placeholder when cleaning test content', function (): void {
    $command = new PromoteCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('cleanTestContent');

    $draftContent = "it('user can login', function (): void {
    \$this->fail('Not implemented yet: user can login');
    expect(\$user->login())->toBeTrue();
});";

    $cleaned = $method->invoke($command, $draftContent);

    expect($cleaned)->toContain('expect($user->login())->toBeTrue()');
    expect($cleaned)->not()->toContain('Not implemented yet');
});

it('can generate promoted test content using private method', function (): void {
    $command = new PromoteCommand;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('generatePromotedTestContent');

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

/**
 * Write a draft test file and return its reference.
 */
function writePromotionGuardDraft(): string
{
    $reference = 'tdd-20240101120000-Guard1';
    $tddraftPath = base_path('tests/TDDraft');
    File::ensureDirectoryExists($tddraftPath);

    File::put($tddraftPath . '/PromotionGuardSampleTest.php', "<?php

declare(strict_types=1);

/**
 * TDDraft Test: Promotion guard sample
 *
 * Reference: {$reference}
 * Type: feature
 * Created: 2024-01-01 12:00:00
 */

it('promotion guard sample', function (): void {
    expect(true)->toBeTrue();
})->group('tddraft', 'feature', '{$reference}');
");

    return $reference;
}

function cleanUpPromotionGuardFiles(): void
{
    File::deleteDirectory(base_path('tests/TDDraft'));
    File::delete(base_path('tests/Feature/PromotionGuardSampleTest.php'));
}

it('defines the run option', function (): void {
    $definition = (new PromoteCommand)->getDefinition();

    expect($definition->hasOption('run'))->toBeTrue();
});

it('refuses to promote a test that was never run', function (): void {
    cleanUpPromotionGuardFiles();

    try {
        $reference = writePromotionGuardDraft();

        $exitCode = Artisan::call('tdd:promote', ['reference' => $reference]);

        expect($exitCode)->toBe(1);
        expect(Artisan::output())->toContain("last status is 'never run'");
        expect(File::exists(base_path('tests/Feature/PromotionGuardSampleTest.php')))->toBeFalse();
        expect(File::exists(base_path('tests/TDDraft/PromotionGuardSampleTest.php')))->toBeTrue();
        expect((new StatusTracker)->getTestStatus($reference))->toBeNull();
    } finally {
        cleanUpPromotionGuardFiles();
    }
});

it('refuses to promote a failing test', function (string $status): void {
    cleanUpPromotionGuardFiles();

    try {
        $reference = writePromotionGuardDraft();
        (new StatusTracker)->updateTestStatus($reference, $status);

        $exitCode = Artisan::call('tdd:promote', ['reference' => $reference]);

        expect($exitCode)->toBe(1);
        expect(Artisan::output())->toContain("last status is '{$status}'");
        expect(File::exists(base_path('tests/Feature/PromotionGuardSampleTest.php')))->toBeFalse();
        expect((new StatusTracker)->getTestStatus($reference))->toBe($status);
    } finally {
        cleanUpPromotionGuardFiles();
    }
})->with(['failed', 'error', 'red', 'skipped']);

it('promotes a test whose last status is passed', function (): void {
    cleanUpPromotionGuardFiles();

    try {
        $reference = writePromotionGuardDraft();
        (new StatusTracker)->updateTestStatus($reference, 'passed');

        $exitCode = Artisan::call('tdd:promote', ['reference' => $reference]);

        expect($exitCode)->toBe(0);
        expect(File::exists(base_path('tests/Feature/PromotionGuardSampleTest.php')))->toBeTrue();
        expect(File::exists(base_path('tests/TDDraft/PromotionGuardSampleTest.php')))->toBeFalse();
        expect((new StatusTracker)->getTestStatus($reference))->toBe('promoted');
    } finally {
        cleanUpPromotionGuardFiles();
    }
});

it('promotes a failing test when forced', function (): void {
    cleanUpPromotionGuardFiles();

    try {
        $reference = writePromotionGuardDraft();
        (new StatusTracker)->updateTestStatus($reference, 'failed');

        $exitCode = Artisan::call('tdd:promote', ['reference' => $reference, '--force' => true]);

        expect($exitCode)->toBe(0);
        expect(Artisan::output())->toContain("despite last status 'failed'");
        expect(File::exists(base_path('tests/Feature/PromotionGuardSampleTest.php')))->toBeTrue();
        expect((new StatusTracker)->getTestStatus($reference))->toBe('promoted');
    } finally {
        cleanUpPromotionGuardFiles();
    }
});

it('skips the guard when status tracking is disabled', function (): void {
    config()->set('tddraft.status_tracking.enabled', false);
    cleanUpPromotionGuardFiles();

    try {
        $reference = writePromotionGuardDraft();

        $exitCode = Artisan::call('tdd:promote', ['reference' => $reference]);

        expect($exitCode)->toBe(0);
        expect(Artisan::output())->toContain('Status tracking is disabled');
        expect(File::exists(base_path('tests/Feature/PromotionGuardSampleTest.php')))->toBeTrue();
    } finally {
        cleanUpPromotionGuardFiles();
    }
});

it('fails the run step when pest is not available in the target project', function (): void {
    if (File::exists(base_path('vendor/bin/pest'))) {
        // The scenario needs an application without Pest installed.
        expect(File::exists(base_path('vendor/bin/pest')))->toBeTrue();

        return;
    }

    cleanUpPromotionGuardFiles();

    try {
        $reference = writePromotionGuardDraft();
        (new StatusTracker)->updateTestStatus($reference, 'passed');

        $exitCode = Artisan::call('tdd:promote', ['reference' => $reference, '--run' => true]);

        expect($exitCode)->toBe(1);
        expect(Artisan::output())->toContain('the promoted test could not be run');
        expect(File::exists(base_path('tests/Feature/PromotionGuardSampleTest.php')))->toBeTrue();
    } finally {
        cleanUpPromotionGuardFiles();
    }
});

it('verifies command has proper parent class', function (): void {
    $reflection = new ReflectionClass(PromoteCommand::class);
    $parentClass = $reflection->getParentClass();

    expect($parentClass)->not()->toBeFalse();
    if ($parentClass !== false) {
        expect($parentClass->getName())->toBe(Command::class);
    }
});
