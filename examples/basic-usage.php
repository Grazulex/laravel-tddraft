<?php

declare(strict_types=1);

/**
 * Basic usage example for Laravel TDDraft
 *
 * This example demonstrates the basic workflow of using Laravel TDDraft
 * for Test-Driven Development in a Laravel application with the current
 * three-command system: tdd:init, tdd:make, and tdd:test.
 */
echo "Laravel TDDraft - Basic Usage Example\n";
echo "=====================================\n\n";

echo "This example shows the complete TDDraft workflow with current commands:\n\n";

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
echo "  - tests/TDDraft/ directory with .gitkeep\n";
echo "  - config/tddraft.php configuration file\n";
echo "  - Updated phpunit.xml with separate testsuites\n";
echo "  - Updated tests/Pest.php to exclude TDDraft from main runs\n";
echo "  - Backup files for safety\n";
echo "  - Optional example test file\n\n";

// Example 3: Creating Draft Tests
echo "3. Creating Draft Tests with tdd:make\n";
echo "-------------------------------------\n";
echo "Use the make command to create tests with unique tracking:\n\n";

echo "  # Create a feature test\n";
echo "  php artisan tdd:make \"User can register\"\n\n";

echo "  # Create a unit test\n";
echo "  php artisan tdd:make \"Password validation\" --type=unit\n\n";

echo "  # Create test in subdirectory\n";
echo "  php artisan tdd:make \"API authentication\" --path=Auth/Api\n\n";

echo "Generated test structure:\n";
$exampleTest = <<<'PHP'
<?php

declare(strict_types=1);

/**
 * TDDraft Test: User can register
 * 
 * Reference: tdd-20250718142530-Abc123
 * Type: feature
 * Created: 2025-07-18 14:25:30
 */

it('user can register', function (): void {
    // TODO: Implement your test scenario here
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
})
->group('tddraft', 'feature', 'tdd-20250718142530-Abc123')
->todo('Implement the test scenario for: user can register');
PHP;

echo $exampleTest . "\n\n";

// Example 4: Running Tests
echo "4. Running Tests with tdd:test\n";
echo "------------------------------\n";
echo "Use the dedicated test command:\n\n";

echo "  # Run all draft tests\n";
echo "  php artisan tdd:test\n\n";

echo "  # Run with filtering\n";
echo "  php artisan tdd:test --filter=\"registration\"\n\n";

echo "  # Run with coverage\n";
echo "  php artisan tdd:test --coverage\n\n";

echo "  # Run with options\n";
echo "  php artisan tdd:test --parallel --stop-on-failure\n\n";

echo "Alternative Pest commands:\n";
echo "  pest --testsuite=tddraft          # Draft tests only\n";
echo "  pest                              # Main tests only (excludes drafts)\n";
echo "  pest --testsuite=default,tddraft  # All tests\n\n";

// Example 5: TDD Workflow with Reference Tracking
echo "5. TDD Workflow with Reference Tracking\n";
echo "---------------------------------------\n";
echo "1. RED: Write failing test using tdd:make\n";
echo "2. GREEN: Implement minimal code to make test pass\n";
echo "3. REFACTOR: Improve code while keeping tests green\n";
echo "4. GRADUATE: Move stable test to main suite with reference tracking\n\n";

echo "Test graduation workflow:\n";
echo "  # 1. Note the unique reference from test header\n";
echo "  # Reference: tdd-20250718142530-Abc123\n\n";

echo "  # 2. Move the test file\n";
echo "  mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/Auth/UserRegistrationTest.php\n\n";

echo "  # 3. Update groups (remove 'tddraft', keep reference)\n";
echo "  # Change: ->group('tddraft', 'feature', 'tdd-20250718142530-Abc123')\n";
echo "  # To:     ->group('feature', 'tdd-20250718142530-Abc123')\n\n";

echo "  # 4. Verify in main test suite\n";
echo "  pest tests/Feature/Auth/UserRegistrationTest.php\n\n";

// Example 6: Advanced Filtering
echo "6. Advanced Filtering with References\n";
echo "-------------------------------------\n";
echo "Filter by test type:\n";
echo "  php artisan tdd:test --group=feature\n";
echo "  pest --testsuite=tddraft --group=unit\n\n";

echo "Filter by specific reference:\n";
echo "  pest --testsuite=tddraft --group=tdd-20250718142530-Abc123\n\n";

echo "Filter by name pattern:\n";
echo "  php artisan tdd:test --filter=\"password\"\n\n";

// Example 7: Workflow Visualization
echo "7. TDDraft → CI Workflow\n";
echo "------------------------\n";
echo "See chart.png for visual representation of:\n";
echo "  ┌─────────────┐    ┌──────────────┐    ┌─────────────┐\n";
echo "  │ tdd:make    │ -> │ tdd:test     │ -> │ Graduate    │\n";
echo "  │ (create)    │    │ (iterate)    │    │ (promote)   │\n";
echo "  └─────────────┘    └──────────────┘    └─────────────┘\n";
echo "       │                     │                   │\n";
echo "  Draft Tests         Red-Green-Refactor    CI Test Suite\n\n";

// Example 8: Configuration
echo "8. Configuration Options\n";
echo "------------------------\n";
echo "The config/tddraft.php file contains:\n";
echo "  - Package enablement settings\n";
echo "  - Default timeouts and retry attempts\n";
echo "  - Caching configuration\n";
echo "  - Logging settings\n\n";

echo "Environment variables:\n";
echo "  LARAVEL_TDDRAFT_ENABLED=true\n";
echo "  LARAVEL_TDDRAFT_LOGGING_ENABLED=false\n";
echo "  LARAVEL_TDDRAFT_LOG_CHANNEL=stack\n";
echo "  LARAVEL_TDDRAFT_LOG_LEVEL=info\n\n";

echo "✅ That's the complete TDDraft workflow!\n";
echo "Key benefits:\n";
echo "  • Three-command simplicity: init → make → test\n";
echo "  • Unique reference tracking for audit trails\n";
echo "  • Clean separation of draft and CI tests\n";
echo "  • Visual workflow guidance with chart.png\n\n";
echo "For more advanced usage, see examples/advanced-usage.php\n";
echo "For complete documentation, see docs/\n";
