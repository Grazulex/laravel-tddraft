<?php

declare(strict_types=1);

/**
 * Basic usage example for Laravel TDDraft
 *
 * This example demonstrates the basic workflow of using Laravel TDDraft
 * for Test-Driven Development in a Laravel application.
 */
echo "Laravel TDDraft - Basic Usage Example\n";
echo "=====================================\n\n";

echo "This example shows the basic TDDraft workflow:\n\n";

// Example 1: Package Installation
echo "1. Package Installation\n";
echo "-----------------------\n";
echo "First, install the package:\n";
echo "  composer require --dev grazulex/laravel-tddraft\n\n";

echo "Install Pest 3 (required):\n";
echo "  composer require pestphp/pest --dev\n";
echo "  php artisan pest:install\n\n";

// Example 2: Environment Setup
echo "2. Environment Setup\n";
echo "--------------------\n";
echo "Initialize your TDDraft environment:\n";
echo "  php artisan vendor:publish --tag=tddraft-config\n";
echo "  php artisan tdd:init\n\n";

echo "This creates:\n";
echo "  - tests/TDDraft/ directory\n";
echo "  - config/tddraft.php configuration file\n";
echo "  - Updated phpunit.xml with separate testsuite\n";
echo "  - Updated tests/Pest.php to exclude TDDraft from main runs\n\n";

// Example 3: Writing Draft Tests
echo "3. Writing Your First Draft Test\n";
echo "---------------------------------\n";
echo "Create a file: tests/TDDraft/UserCanRegisterTest.php\n\n";

$exampleTest = <<<'PHP'
<?php

declare(strict_types=1);

// This test will start failing (RED phase) - that's expected in TDD
it('allows user registration with valid data', function (): void {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
})->group('tddraft');

it('validates required fields during registration', function (): void {
    $response = $this->post('/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
})->group('tddraft');
PHP;

echo "Example test content:\n";
echo $exampleTest . "\n\n";

// Example 4: Running Tests
echo "4. Running Tests\n";
echo "----------------\n";
echo "Run only your draft tests:\n";
echo "  pest --testsuite=tddraft\n\n";

echo "Run your main test suite (excludes drafts):\n";
echo "  pest\n\n";

echo "Run all tests:\n";
echo "  pest --testsuite=default,tddraft\n\n";

// Example 5: TDD Workflow
echo "5. TDD Workflow\n";
echo "---------------\n";
echo "1. RED: Write failing test (above)\n";
echo "2. GREEN: Implement minimal code to make test pass\n";
echo "3. REFACTOR: Improve code while keeping tests green\n";
echo "4. GRADUATE: Move stable test to main suite\n\n";

echo "To graduate a test:\n";
echo "  mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/UserRegistrationTest.php\n";
echo "  # Edit file to remove ->group('tddraft')\n\n";

// Example 6: Configuration
echo "6. Configuration Options\n";
echo "------------------------\n";
echo "The config/tddraft.php file contains:\n";
echo "  - Package enablement settings\n";
echo "  - Default timeouts and retry attempts\n";
echo "  - Caching configuration\n";
echo "  - Logging settings\n\n";

echo "Environment variables:\n";
echo "  LARAVEL_TDDRAFT_ENABLED=true\n";
echo "  LARAVEL_TDDRAFT_LOGGING_ENABLED=false\n\n";

echo "✅ That's the basic workflow!\n";
echo "For more advanced usage, see examples/advanced-usage.php\n";
echo "For complete documentation, see docs/\n";
