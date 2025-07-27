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

// Example 4: Advanced Filtering and Group Options
echo "4. Advanced Filtering and Group Options\n";
echo "=======================================\n";
echo "Laravel TDDraft provides powerful filtering capabilities across all commands:\n\n";

echo "A. tdd:test Command Filtering:\n";
echo "------------------------------\n";
echo "  # Filter by test name or description\n";
echo "  php artisan tdd:test --filter=\"registration\"\n";
echo "  php artisan tdd:test --filter=\"user login\"\n\n";

echo "  # Filter by specific reference (exact)\n";
echo "  php artisan tdd:test --filter=\"tdd-20250727142530-Abc123\"\n\n";

echo "  # Filter by partial reference (time-based)\n";
echo "  php artisan tdd:test --filter=\"tdd-20250727\"        # All tests from specific day\n";
echo "  php artisan tdd:test --filter=\"tdd-202507271425\"    # Tests from specific time\n\n";

echo "  # Combine with performance options\n";
echo "  php artisan tdd:test --filter=\"api\" --coverage --parallel\n";
echo "  php artisan tdd:test --filter=\"authentication\" --stop-on-failure\n\n";

echo "B. Pest Group System Filtering:\n";
echo "-------------------------------\n";
echo "Every test gets multiple groups for flexible filtering:\n";
echo "  • 'tddraft' - All TDDraft tests\n";
echo "  • 'feature' or 'unit' - Test type\n";
echo "  • 'tdd-YYYYMMDDHHMMSS-RANDOM' - Unique reference\n\n";

echo "  # Run only feature draft tests\n";
echo "  pest --testsuite=tddraft --group=feature\n\n";

echo "  # Run only unit draft tests\n";
echo "  pest --testsuite=tddraft --group=unit\n\n";

echo "  # Run specific test by reference\n";
echo "  pest --testsuite=tddraft --group=tdd-20250727142530-Abc123\n\n";

echo "  # Combine groups and options\n";
echo "  pest --testsuite=tddraft --group=feature,unit --parallel\n";
echo "  pest --testsuite=tddraft --group=feature --coverage\n\n";

echo "C. tdd:list Command Filtering:\n";
echo "------------------------------\n";
echo "  # Filter by test type\n";
echo "  php artisan tdd:list --type=feature\n";
echo "  php artisan tdd:list --type=unit\n\n";

echo "  # Filter by directory path\n";
echo "  php artisan tdd:list --path=Auth\n";
echo "  php artisan tdd:list --path=Api/V1\n";
echo "  php artisan tdd:list --path=Services/Payment\n\n";

echo "  # Show detailed view with status information\n";
echo "  php artisan tdd:list --details\n\n";

echo "  # Combine filters for precise results\n";
echo "  php artisan tdd:list --type=feature --path=Auth --details\n";
echo "  php artisan tdd:list --type=unit --path=Services\n\n";

echo "D. Status-Based Filtering (for test management):\n";
echo "------------------------------------------------\n";
echo "  # Find tests ready for promotion\n";
echo "  php artisan tdd:list --details | grep \"✅ Passed\"\n\n";

echo "  # Find tests needing attention\n";
echo "  php artisan tdd:list --details | grep \"❌ Failed\"\n\n";

echo "  # Get references for batch operations\n";
echo "  php artisan tdd:list --type=feature | grep \"tdd-\"\n\n";

echo "E. Reference-Based Operations:\n";
echo "------------------------------\n";
echo "Each test gets a unique reference for precise tracking:\n\n";

echo "  # Create test (generates unique reference)\n";
echo "  php artisan tdd:make \"User login validation\"\n";
echo "  # Output: Reference: tdd-20250727142530-Abc123\n\n";

echo "  # Run specific test by exact reference\n";
echo "  php artisan tdd:test --filter=\"tdd-20250727142530-Abc123\"\n\n";

echo "  # Promote specific test by reference\n";
echo "  php artisan tdd:promote tdd-20250727142530-Abc123\n\n";

echo "  # Find references using list command\n";
echo "  php artisan tdd:list | grep \"tdd-\" | head -5\n\n";

// Example 5: Running Tests with Status Tracking
echo "5. Running Tests with Status Tracking\n";
echo "=====================================\n";
echo "Use the dedicated test command with automatic status tracking:\n\n";

echo "  # Run all draft tests (with automatic status tracking)\n";
echo "  php artisan tdd:test\n\n";

echo "  # Run with filtering\n";
echo "  php artisan tdd:test --filter=\"registration\"\n\n";

echo "  # Run with coverage\n";
echo "  php artisan tdd:test --coverage\n\n";

echo "  # Run with options\n";
echo "  php artisan tdd:test --parallel --stop-on-failure\n\n";

echo "NEW: Status Tracking Features:\n";
echo "  • Test results are automatically tracked\n";
echo "  • Status history is maintained for each test reference\n";
echo "  • Results saved to tests/TDDraft/.status.json\n";
echo "  • Links outcomes to unique test references\n\n";

echo "Example status file content:\n";
$statusExample = <<<'JSON'
{
  "tdd-20250718142530-Abc123": {
    "status": "passed",
    "updated_at": "2025-07-18T14:30:45+00:00",
    "history": [
      {
        "status": "failed",
        "timestamp": "2025-07-18T14:25:30+00:00"
      }
    ]
  }
}
JSON;
echo $statusExample . "\n\n";

echo "Example tdd:test output with status tracking:\n";
$testOutput = <<<'OUTPUT'
🧪 Running TDDraft tests...

📋 Found TDDraft tests:
  🔖 tdd-20250718142530-Abc123 - User can register (UserCanRegisterTest.php)
  🔖 tdd-20250718141045-Def456 - Password validation (PasswordValidationTest.php)

   PASS  Tests\TDDraft\UserCanRegisterTest
  ✓ user can register

   FAIL  Tests\TDDraft\PasswordValidationTest  
  ⨯ password must be at least 8 characters

Tests:  1 passed, 1 failed
Time:   0.15s

⚠️  Some TDDraft tests failed (this is normal during TDD red phase)

📊 Test statuses updated in tests/TDDraft/.status.json
OUTPUT;

echo $testOutput . "\n\n";

echo "Alternative Pest commands:\n";
echo "  pest --testsuite=tddraft          # Draft tests only\n";
echo "  pest                              # Main tests only (excludes drafts)\n";
echo "  pest --testsuite=default,tddraft  # All tests\n\n";

// Example 6: TDD Workflow with Reference Tracking and Promotion
echo "6. Complete TDD Workflow with All Commands\n";
echo "==========================================\n";
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

// Example 7: Real-World Filtering Examples
echo "7. Real-World Filtering Examples\n";
echo "================================\n";
echo "Practical examples of using filters in development workflows:\n\n";

echo "Scenario A: Feature Development\n";
echo "-------------------------------\n";
echo "  # Create tests for authentication feature\n";
echo "  php artisan tdd:make \"User can login\" --path=Auth/Login\n";
echo "  php artisan tdd:make \"User can logout\" --path=Auth/Login\n";
echo "  php artisan tdd:make \"Login validation\" --type=unit --path=Auth\n\n";

echo "  # Run all authentication tests\n";
echo "  php artisan tdd:test --filter=\"login\"\n";
echo "  php artisan tdd:list --path=Auth --details\n\n";

echo "  # Run only login feature tests\n";
echo "  php artisan tdd:list --type=feature --path=Auth/Login\n";
echo "  php artisan tdd:test --filter=\"login\" --coverage\n\n";

echo "Scenario B: API Development\n";
echo "---------------------------\n";
echo "  # Create API tests\n";
echo "  php artisan tdd:make \"API user registration\" --path=Api/V1\n";
echo "  php artisan tdd:make \"API validation rules\" --type=unit --path=Api\n\n";

echo "  # Filter and test API functionality\n";
echo "  php artisan tdd:list --path=Api --details\n";
echo "  php artisan tdd:test --filter=\"api\" --parallel\n\n";

echo "Scenario C: Bug Fixing\n";
echo "----------------------\n";
echo "  # Create test for specific bug\n";
echo "  php artisan tdd:make \"Password reset bug fix\"\n";
echo "  # Reference: tdd-20250727142530-Bug123\n\n";

echo "  # Test specific bug fix\n";
echo "  php artisan tdd:test --filter=\"tdd-20250727142530-Bug123\"\n\n";

echo "  # Monitor until fixed, then promote\n";
echo "  php artisan tdd:promote tdd-20250727142530-Bug123\n\n";

echo "Scenario D: Batch Management\n";
echo "----------------------------\n";
echo "  # Find all stable feature tests\n";
echo "  php artisan tdd:list --type=feature --details | grep \"✅ Passed\"\n\n";

echo "  # Batch promote stable tests\n";
echo "  # (copy references from above output)\n";
echo "  php artisan tdd:promote tdd-20250727142530-Abc123\n";
echo "  php artisan tdd:promote tdd-20250727142531-Def456\n\n";

echo "Scenario E: Time-Based Analysis\n";
echo "------------------------------\n";
echo "  # Tests created today\n";
echo "  php artisan tdd:list | grep \"tdd-20250727\"\n\n";

echo "  # Tests from specific hour (debugging session)\n";
echo "  php artisan tdd:test --filter=\"tdd-202507271425\"\n\n";

echo "  # All tests from morning work session\n";
echo "  php artisan tdd:list --details | grep \"09:\"\n\n";

// Example 8: List and Manage Tests
echo "8. List and Manage Tests with tdd:list\n";
echo "======================================\n";
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

echo "Example output with status tracking:\n";
$listOutput = <<<'OUTPUT'
📋 TDDraft Tests List
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

┌──────────────────────────┬─────────────────────────────────────────┬─────────┬─────────────┬─────────────────────────┐
│ Reference                │ Name                                    │ Type    │ Status      │ File                    │
├──────────────────────────┼─────────────────────────────────────────┼─────────┼─────────────┼─────────────────────────┤
│ tdd-20250718142530-Abc123│ User can register                       │ feature │ ✅ Passed   │ UserCanRegisterTest.php │
│ tdd-20250718141045-Def456│ Password validation                     │ unit    │ ❌ Failed   │ PasswordValidationTest.php│
└──────────────────────────┴─────────────────────────────────────────┴─────────┴─────────────┴─────────────────────────┘

📊 Total: 2 draft test(s)

💡 Tips:
  • Run specific test: php artisan tdd:test --filter="<reference>"
  • Run by type: php artisan tdd:test --filter="feature"
  • Promote draft: php artisan tdd:promote <reference>
OUTPUT;

echo $listOutput . "\n\n";

// Example 9: Promote Tests
echo "9. Promote Tests with tdd:promote\n";
echo "=================================\n";
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
echo "  - Status tracking configuration (NEW)\n";
echo "  - Test result history management\n";
echo "  - File path settings for status storage\n";
echo "  - Environment-specific controls\n\n";

echo "Environment variables:\n";
echo "  LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=true\n";
echo "  LARAVEL_TDDRAFT_STATUS_FILE=tests/TDDraft/.status.json\n";
echo "  LARAVEL_TDDRAFT_TRACK_HISTORY=true\n";
echo "  LARAVEL_TDDRAFT_MAX_HISTORY=50\n\n";

echo "✅ That's the complete TDDraft workflow!\n";
echo "Key benefits:\n";
echo "  • Five-command completeness: init → make → test → list → promote\n";
echo "  • Unique reference tracking for audit trails\n";
echo "  • **Comprehensive status tracking** with automatic monitoring\n";
echo "  • **Historical status data** for test evolution analysis\n";
echo "  • Clean separation of draft and CI tests\n";
echo "  • Automated promotion with tdd:promote\n";
echo "  • **Data-driven test management** with tdd:list\n";
echo "  • Visual workflow guidance with chart.png\n\n";
echo "For more advanced usage, see examples/advanced-usage.php\n";
echo "For complete documentation, see docs/\n";
