<?php

declare(strict_types=1);

/**
 * Basic usage example for Laravel TDDraft
 *
 * This example demonstrates the complete workflow of using Laravel TDDraft
 * for Test-Driven Development in a Laravel application with all five
 * commands: tdd:init, tdd:make, tdd:test, tdd:list, and tdd:promote.
 */
echo "Laravel TDDraft - Basic Usage Example\n";
echo "=====================================\n\n";

echo "This example shows the complete TDDraft workflow with all five commands:\n\n";

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

// Example 5: TDD Workflow with Reference Tracking and Promotion
echo "5. Complete TDD Workflow with All Commands\n";
echo "------------------------------------------\n";
echo "1. INIT: Set up environment with tdd:init\n";
echo "2. CREATE: Write failing test using tdd:make\n";
echo "3. RED: Run test and see it fail with tdd:test\n";
echo "4. GREEN: Implement minimal code to make test pass\n";
echo "5. REFACTOR: Improve code while keeping tests green\n";
echo "6. REVIEW: Use tdd:list to manage your draft tests\n";
echo "7. PROMOTE: Move stable test to CI suite with tdd:promote\n\n";

echo "Complete workflow example:\n";
echo "  # 1. Initialize\n";
echo "  php artisan tdd:init\n\n";

echo "  # 2. Create test\n";
echo "  php artisan tdd:make \"User can register\"\n\n";

echo "  # 3. Run and iterate\n";
echo "  php artisan tdd:test\n\n";

echo "  # 4. List and review\n";
echo "  php artisan tdd:list\n\n";

echo "  # 5. Promote when ready\n";
echo "  php artisan tdd:promote tdd-20250718142530-Abc123\n\n";

echo "Test graduation workflow:\n";
echo "  # Find the reference from tdd:list output\n";
echo "  php artisan tdd:list\n\n";

echo "  # Promote using the reference\n";
echo "  php artisan tdd:promote tdd-20250718142530-Abc123\n\n";

echo "  # Verify promoted test works\n";
echo "  pest tests/Feature/UserCanRegisterTest.php\n\n";

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

// Example 7: List and Manage Tests
echo "7. List and Manage Tests with tdd:list\n";
echo "--------------------------------------\n";
echo "Use the list command to view and manage your draft tests:\n\n";

echo "  # List all draft tests\n";
echo "  php artisan tdd:list\n\n";

echo "  # Show detailed information\n";
echo "  php artisan tdd:list --details\n\n";

echo "  # Filter by test type\n";
echo "  php artisan tdd:list --type=feature\n";
echo "  php artisan tdd:list --type=unit\n\n";

echo "  # Filter by directory path\n";
echo "  php artisan tdd:list --path=Auth\n\n";

echo "Example output:\n";
$listOutput = <<<'OUTPUT'
📋 TDDraft Tests List
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

┌──────────────────────────┬─────────────────────────────────────────┬─────────┬─────────────────────────┐
│ Reference                │ Name                                    │ Type    │ File                    │
├──────────────────────────┼─────────────────────────────────────────┼─────────┼─────────────────────────┤
│ tdd-20250718142530-Abc123│ User can register                       │ feature │ UserCanRegisterTest.php │
│ tdd-20250718141045-Def456│ Password validation                     │ unit    │ PasswordValidationTest.php│
└──────────────────────────┴─────────────────────────────────────────┴─────────┴─────────────────────────┘

📊 Total: 2 draft test(s)

💡 Tips:
  • Run specific test: php artisan tdd:test --filter="<reference>"
  • Run by type: php artisan tdd:test --filter="feature"
  • Promote draft: php artisan tdd:promote <reference>
OUTPUT;

echo $listOutput . "\n\n";

// Example 8: Promote Tests
echo "8. Promote Tests with tdd:promote\n";
echo "---------------------------------\n";
echo "Use the promote command to move ready tests to CI suite:\n\n";

echo "  # Basic promotion (auto-detects target directory)\n";
echo "  php artisan tdd:promote tdd-20250718142530-Abc123\n\n";

echo "  # Promote to specific directory\n";
echo "  php artisan tdd:promote tdd-20250718142530-Abc123 --target=Unit\n\n";

echo "  # Promote with custom file name\n";
echo "  php artisan tdd:promote tdd-20250718142530-Abc123 --new-file=UserRegistrationTest\n\n";

echo "  # Keep the original draft file\n";
echo "  php artisan tdd:promote tdd-20250718142530-Abc123 --keep-draft\n\n";

echo "Example output:\n";
echo "  📋 Found draft test: tests/TDDraft/UserCanRegisterTest.php\n";
echo "  ✅ Successfully promoted test to: tests/Feature/UserCanRegisterTest.php\n";
echo "  🎯 Test class: UserCanRegisterTest\n";
echo "  🗑️  Removed draft file: tests/TDDraft/UserCanRegisterTest.php\n\n";

// Example 9: Workflow Visualization
echo "9. Complete TDDraft → CI Workflow\n";
echo "---------------------------------\n";
echo "See chart.png for visual representation of:\n";
echo "  ┌─────────────┐    ┌──────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐\n";
echo "  │ tdd:init    │ -> │ tdd:make     │ -> │ tdd:test    │ -> │ tdd:list    │ -> │ tdd:promote │\n";
echo "  │ (setup)     │    │ (create)     │    │ (iterate)   │    │ (manage)    │    │ (graduate)  │\n";
echo "  └─────────────┘    └──────────────┘    └─────────────┘    └─────────────┘    └─────────────┘\n";
echo "       │                     │                   │                   │                   │\n";
echo "  Environment          Draft Tests        Red-Green-Refactor    Review & Plan       CI Test Suite\n\n";

// Example 10: Configuration
echo "10. Configuration Options\n";
echo "-------------------------\n";
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
echo "  • Five-command completeness: init → make → test → list → promote\n";
echo "  • Unique reference tracking for audit trails\n";
echo "  • Clean separation of draft and CI tests\n";
echo "  • Automated promotion with tdd:promote\n";
echo "  • Test management with tdd:list\n";
echo "  • Visual workflow guidance with chart.png\n\n";
echo "For more advanced usage, see examples/advanced-usage.php\n";
echo "For complete documentation, see docs/\n";
