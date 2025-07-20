<?php

declare(strict_types=1);

/**
 * Advanced usage example for Laravel TDDraft
 *
 * This example demonstrates advanced patterns and best practices
 * for using Laravel TDDraft in complex Laravel applications with
 * all five commands including advanced promotion and list management.
 */
echo "Laravel TDDraft - Advanced Usage Example\n";
echo "=========================================\n\n";

echo "This example covers advanced TDDraft patterns, automated promotion,\n";
echo "list management, and complex workflows for enterprise applications.\n\n";

// Example 1: Advanced Test Creation with References
echo "1. Advanced Test Creation with tdd:make\n";
echo "---------------------------------------\n";
echo "Create complex tests with organized structure:\n\n";

echo "# Create E2E test in subdirectory\n";
echo "php artisan tdd:make \"Complete order workflow\" --path=E2E --type=feature\n\n";

echo "# Create API integration test\n";
echo "php artisan tdd:make \"Payment gateway integration\" --path=Integrations/Payment --type=unit\n\n";

echo "# Create performance test\n";
echo "php artisan tdd:make \"Database query performance\" --path=Performance --class=DatabasePerformanceTest\n\n";

echo "Generated test with enhanced tracking:\n";

$complexTest = <<<'PHP'
<?php

declare(strict_types=1);

/**
 * TDDraft Test: Complete order workflow
 * 
 * Reference: tdd-20250718151230-E2e987
 * Type: feature
 * Created: 2025-07-18 15:12:30
 * Path: tests/TDDraft/E2E/CompleteOrderWorkflowTest.php
 */

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

uses(RefreshDatabase::class);

it('complete order workflow', function (): void {
    // Arrange: Set up test data with factories
    Mail::fake();
    
    $user = User::factory()->create([
        'email' => 'customer@example.com'
    ]);
    
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 29.99,
        'stock' => 10
    ]);

    // Act: Simulate complete order process
    $response = $this->actingAs($user)
        ->post('/api/orders', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ],
            'shipping_address' => [
                'street' => '123 Test St',
                'city' => 'Test City',
                'zip' => '12345'
            ]
        ]);

    // Assert: Verify complete workflow
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id', 'total', 'status', 'items'
            ]
        ]);
    
    $order = Order::where('user_id', $user->id)->first();
    expect($order)->not->toBeNull();
    expect($order->total)->toBe(59.98); // 29.99 * 2
    expect($order->status)->toBe('pending');
    
    // Verify stock was decremented
    expect($product->fresh()->stock)->toBe(8);
    
    // Verify email was sent
    Mail::assertSent(OrderConfirmationMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
})
->group('tddraft', 'feature', 'tdd-20250718151230-E2e987', 'e2e', 'orders')
->todo('Implement complete order processing workflow');
PHP;

echo $complexTest . "\n\n";

// Example 2: Advanced Testing Strategies
echo "2. Advanced Testing Strategies with References\n";
echo "----------------------------------------------\n";
echo "Run targeted test subsets using reference tracking:\n\n";

echo "# Run all E2E tests\n";
echo "php artisan tdd:test --group=e2e\n\n";

echo "# Run specific feature by reference\n";
echo "php artisan tdd:test --group=tdd-20250718151230-E2e987\n\n";

echo "# Run all payment-related tests\n";
echo "php artisan tdd:test --filter=payment\n\n";

echo "# Run performance tests only\n";
echo "php artisan tdd:test --group=performance --parallel\n\n";

// Example 3: Test Organization
echo "3. Enterprise Test Organization\n";
echo "-------------------------------\n";
echo "Structure tests by domain and feature:\n\n";

echo "tests/TDDraft/\n";
echo "├── Auth/\n";
echo "│   ├── UserRegistrationTest.php\n";
echo "│   ├── PasswordResetTest.php\n";
echo "│   └── LoginTest.php\n";
echo "├── Blog/\n";
echo "│   ├── PostCreationTest.php\n";
echo "│   ├── CommentSystemTest.php\n";
echo "│   └── PublishingWorkflowTest.php\n";
echo "├── E2E/\n";
echo "│   ├── OrderProcessingTest.php\n";
echo "│   └── UserOnboardingTest.php\n";
echo "└── API/\n";
echo "    ├── AuthenticationTest.php\n";
echo "    └── ResourceCrudTest.php\n\n";

// Example 3: Advanced Test Patterns
echo "3. Advanced Test Patterns\n";
echo "-------------------------\n";

$advancedPatterns = <<<'PHP'
// Use beforeEach for common setup
beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    $this->user = User::factory()->create();
})->group('tddraft');

// Parameterized tests for multiple scenarios
it('validates different user input scenarios', function (array $input, array $expectedErrors): void {
    $response = $this->post('/api/users', $input);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrors);
})->with([
    'missing email' => [
        ['name' => 'John'], 
        ['email']
    ],
    'invalid email format' => [
        ['name' => 'John', 'email' => 'invalid'], 
        ['email']
    ],
    'missing required fields' => [
        [], 
        ['name', 'email']
    ]
])->group('tddraft');

// API testing with authentication
it('requires authentication for protected endpoints', function (string $endpoint): void {
    $response = $this->getJson($endpoint);
    $response->assertStatus(401);
})->with([
    '/api/profile',
    '/api/orders',
    '/api/admin/users'
])->group('tddraft');

// Performance testing considerations
it('handles bulk operations efficiently', function (): void {
    $products = Product::factory()->count(1000)->create();
    
    $startTime = microtime(true);
    
    $response = $this->getJson('/api/products?per_page=100');
    
    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;
    
    $response->assertStatus(200);
    expect($executionTime)->toBeLessThan(1.0); // Should complete within 1 second
})->group('tddraft');
PHP;

echo "Advanced test patterns:\n";
echo $advancedPatterns . "\n\n";

// Example 4: Configuration Management with Status Tracking
echo "4. Configuration Management with Status Tracking\n";
echo "------------------------------------------------\n";
echo "Advanced configuration for different environments:\n\n";

$configExample = <<<'PHP'
// config/tddraft.php - Environment-specific settings with status tracking
return [
    /**
     * NEW: Test status tracking configuration
     * 
     * Tracks test execution results and maintains history for audit trails
     */
    'status_tracking' => [
        // Enable tracking in development and testing environments
        'enabled' => env('LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED', 
            app()->environment('local', 'testing')),

        // Environment-specific status file paths
        'file_path' => env('LARAVEL_TDDRAFT_STATUS_FILE', 
            'tests/TDDraft/.status.' . app()->environment() . '.json'),

        // Track detailed history in development, minimal in testing
        'track_history' => env('LARAVEL_TDDRAFT_TRACK_HISTORY', 
            app()->environment('local')),

        // Adjust history limits per environment
        'max_history_entries' => env('LARAVEL_TDDRAFT_MAX_HISTORY', 
            app()->environment('local') ? 100 : 20),
    ],
];

// Environment files (.env.local, .env.testing, etc.)
// .env.local (development)
LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=true
LARAVEL_TDDRAFT_TRACK_HISTORY=true  
LARAVEL_TDDRAFT_MAX_HISTORY=100

// .env.testing (CI/testing)
LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=true
LARAVEL_TDDRAFT_TRACK_HISTORY=false
LARAVEL_TDDRAFT_MAX_HISTORY=20

// .env.production (disabled)
LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=false
PHP;

echo $configExample . "\n\n";

// Example 5: Custom Test Helpers
echo "5. Custom Test Helpers\n";
echo "----------------------\n";

$helpersExample = <<<'PHP'
// tests/TDDraft/Helpers/TddraftHelpers.php
<?php

declare(strict_types=1);

if (!function_exists('createAuthenticatedUser')) {
    function createAuthenticatedUser(array $attributes = []): \App\Models\User
    {
        $user = \App\Models\User::factory()->create($attributes);
        \Illuminate\Support\Facades\Auth::login($user);
        return $user;
    }
}

if (!function_exists('assertApiResponse')) {
    function assertApiResponse($response, int $status = 200, array $structure = []): void
    {
        $response->assertStatus($status);
        
        if (!empty($structure)) {
            $response->assertJsonStructure($structure);
        }
        
        $response->assertHeader('Content-Type', 'application/json');
    }
}

// Usage in tests
it('returns user profile data', function (): void {
    $user = createAuthenticatedUser(['name' => 'John Doe']);
    
    $response = $this->getJson('/api/profile');
    
    assertApiResponse($response, 200, [
        'data' => ['id', 'name', 'email', 'created_at']
    ]);
})->group('tddraft');
PHP;

echo "Custom helper functions:\n";
echo $helpersExample . "\n\n";

// Example 6: Status Tracking Analysis
echo "6. Status Tracking Analysis (NEW)\n";
echo "---------------------------------\n";
echo "Advanced usage of status tracking data for test management:\n\n";

$statusAnalysisExample = <<<'PHP'
// Custom script: analyze-test-stability.php
<?php

/**
 * Analyze TDDraft test stability using status tracking data
 * Run: php scripts/analyze-test-stability.php
 */

$statusFile = base_path('tests/TDDraft/.status.json');

if (!file_exists($statusFile)) {
    echo "❌ No status tracking data found\n";
    exit(1);
}

$statuses = json_decode(file_get_contents($statusFile), true);

if (empty($statuses)) {
    echo "📊 No test data available\n";
    exit(0);
}

echo "📈 TDDraft Test Stability Analysis\n";
echo "==================================\n\n";

$stable = [];
$unstable = [];
$failing = [];

foreach ($statuses as $reference => $data) {
    $historyCount = count($data['history']);
    $currentStatus = $data['status'];
    
    if ($currentStatus === 'passed' && $historyCount === 0) {
        $stable[] = $reference;
    } elseif ($historyCount > 3) {
        $unstable[] = $reference;
    } elseif ($currentStatus === 'failed') {
        $failing[] = $reference;
    }
}

echo "✅ Stable Tests (ready for promotion): " . count($stable) . "\n";
foreach ($stable as $ref) {
    echo "   • {$ref}\n";
}

echo "\n⚠️  Unstable Tests (needs attention): " . count($unstable) . "\n";
foreach ($unstable as $ref) {
    $historyCount = count($statuses[$ref]['history']);
    echo "   • {$ref} - {$historyCount} status changes\n";
}

echo "\n❌ Currently Failing Tests: " . count($failing) . "\n";
foreach ($failing as $ref) {
    $lastUpdate = $statuses[$ref]['updated_at'];
    echo "   • {$ref} - failed since {$lastUpdate}\n";
}

// Generate promotion recommendations
echo "\n🚀 Promotion Recommendations:\n";
foreach ($stable as $ref) {
    echo "   php artisan tdd:promote {$ref}\n";
}
PHP;

echo $statusAnalysisExample . "\n\n";

// Example 7: Integration with CI/CD
echo "7. CI/CD Integration with Status Tracking\n";
echo "-----------------------------------------\n";
echo "GitHub Actions workflow for TDDraft with status tracking:\n\n";

$ciExample = <<<'YAML'
# .github/workflows/tddraft.yml
name: TDDraft Tests

on:
  push:
    branches: [ develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  tddraft:
    runs-on: ubuntu-latest
    if: contains(github.event.head_commit.message, '[tddraft]')
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite
    
    - name: Install dependencies
      run: composer install --no-interaction --prefer-dist --optimize-autoloader
    
    - name: Run TDDraft tests
      run: |
        php artisan tdd:init --no-interaction
        ./vendor/bin/pest --testsuite=tddraft --coverage
YAML;

echo $ciExample . "\n\n";

// Example 7: Performance Monitoring
echo "7. Performance Monitoring\n";
echo "-------------------------\n";
echo "Monitor test execution times:\n\n";

$performanceExample = <<<'PHP'
// Add to tests/TDDraft/Performance/PerformanceTest.php
it('tracks test execution performance', function (): void {
    $startMemory = memory_get_usage();
    $startTime = microtime(true);
    
    // Your test logic here
    $response = $this->getJson('/api/large-dataset');
    
    $endTime = microtime(true);
    $endMemory = memory_get_usage();
    
    $executionTime = $endTime - $startTime;
    $memoryUsed = $endMemory - $startMemory;
    
    // Log performance metrics
    Log::info('TDDraft Performance Metrics', [
        'test' => 'large-dataset-retrieval',
        'execution_time' => $executionTime,
        'memory_used' => $memoryUsed,
        'response_size' => strlen($response->getContent())
    ]);
    
    expect($executionTime)->toBeLessThan(2.0);
    expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024); // 50MB
})->group('tddraft');
PHP;

echo $performanceExample . "\n\n";

// Example 8: Advanced List Management with tdd:list
echo "8. Advanced List Management with tdd:list\n";
echo "----------------------------------------\n";
echo "Professional test management and filtering:\n\n";

echo "# List with detailed metadata\n";
echo "php artisan tdd:list --details\n\n";

echo "# Filter by specific areas\n";
echo "php artisan tdd:list --path=E2E\n";
echo "php artisan tdd:list --path=Integrations\n";
echo "php artisan tdd:list --path=Performance\n\n";

echo "# Filter by test types for targeted review\n";
echo "php artisan tdd:list --type=feature  # Focus on integration tests\n";
echo "php artisan tdd:list --type=unit     # Focus on unit tests\n\n";

echo "Example detailed output for complex project with status tracking:\n";
$advancedListOutput = <<<'OUTPUT'
📋 TDDraft Tests List
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🔖 tdd-20250718151230-E2e987
📝 Complete order workflow
📁 E2E/CompleteOrderWorkflowTest.php
🏷️  feature
📊 ✅ Passed
📅 2025-07-18 15:12:30
────────────────────────────────────────────────────────────

🔖 tdd-20250718152045-Pay456
📝 Payment gateway integration
📁 Integrations/Payment/PaymentGatewayIntegrationTest.php
🏷️  unit
📊 ❌ Failed
📅 2025-07-18 15:20:45
────────────────────────────────────────────────────────────

🔖 tdd-20250718153010-Perf123
📝 Database query performance
📁 Performance/DatabasePerformanceTest.php
🏷️  unit
📊 💥 Error
📅 2025-07-18 15:30:10
────────────────────────────────────────────────────────────

📊 Total: 3 draft test(s)

💡 Tips:
  • Run specific test: php artisan tdd:test --filter="<reference>"
  • Run by type: php artisan tdd:test --filter="feature"
  • Promote draft: php artisan tdd:promote <reference>
OUTPUT;

echo $advancedListOutput . "\n\n";

echo "# Organizational use cases:\n";
echo "# Review all E2E tests before sprint demo\n";
echo "php artisan tdd:list --path=E2E --details\n\n";

echo "# Check performance tests before optimization sprint\n";
echo "php artisan tdd:list --path=Performance\n\n";

echo "# Review unit tests for specific component\n";
echo "php artisan tdd:list --path=Services/Payment --type=unit\n\n";

// Example 9: Advanced Promotion with tdd:promote
echo "9. Advanced Promotion with tdd:promote\n";
echo "-------------------------------------\n";
echo "Enterprise-grade test promotion strategies:\n\n";

echo "# Promote E2E test to Feature directory\n";
echo "php artisan tdd:promote tdd-20250718151230-E2e987 --target=Feature --new-file=OrderWorkflowE2ETest\n\n";

echo "# Promote integration test to Unit directory with custom organization\n";
echo "php artisan tdd:promote tdd-20250718152045-Pay456 --target=Unit --new-file=Integrations/PaymentGatewayTest\n\n";

echo "# Append performance test to existing performance test suite\n";
echo "php artisan tdd:promote tdd-20250718153010-Perf123 --file=Performance/DatabasePerformanceTest.php\n\n";

echo "# Promote with backup (keep draft for reference)\n";
echo "php artisan tdd:promote tdd-20250718151230-E2e987 --keep-draft --new-file=CompleteOrderWorkflowTest\n\n";

echo "# Batch promotion workflow for end-of-sprint\n";
echo "# Step 1: Review what's ready\n";
echo "php artisan tdd:list --details\n\n";

echo "# Step 2: Promote feature tests\n";
echo "php artisan tdd:promote tdd-20250718151230-E2e987 --target=Feature\n";
echo "php artisan tdd:promote tdd-20250718151245-Api789 --target=Feature\n\n";

echo "# Step 3: Promote unit tests\n";
echo "php artisan tdd:promote tdd-20250718152045-Pay456 --target=Unit\n";
echo "php artisan tdd:promote tdd-20250718153010-Perf123 --target=Unit\n\n";

echo "# Step 4: Verify promoted tests in CI\n";
echo "pest tests/Feature/ --stop-on-failure\n";
echo "pest tests/Unit/ --stop-on-failure\n\n";

echo "Advanced promotion example with custom organization:\n";
$promotionExample = <<<'OUTPUT'
$ php artisan tdd:promote tdd-20250718151230-E2e987 --target=Feature --new-file=E2E/OrderWorkflowTest

📋 Found draft test: tests/TDDraft/E2E/CompleteOrderWorkflowTest.php
✅ Successfully promoted test to: tests/Feature/E2E/OrderWorkflowTest.php
🎯 Test class: OrderWorkflowTest
🗑️  Removed draft file: tests/TDDraft/E2E/CompleteOrderWorkflowTest.php

Generated promoted test:
  • Removed TDDraft-specific comments
  • Updated groups: removed 'tddraft', kept 'tdd-20250718151230-E2e987'
  • Preserved reference for audit trail
  • Created directory structure: tests/Feature/E2E/
OUTPUT;

echo $promotionExample . "\n\n";

// Example 10: Advanced Graduation Workflow
echo "10. Advanced Graduation Workflow\n";
echo "--------------------------------\n";
echo "Systematic approach to promoting tests from TDDraft to CI:\n\n";

echo "# Step 1: Review test readiness\n";
echo "php artisan tdd:test --group=tdd-20250718151230-E2e987\n\n";

echo "# Step 2: Automated graduation script (create as: scripts/graduate-test.sh)\n";
$graduationScript = <<<'BASH'
#!/bin/bash
# Graduate TDDraft test to main suite with reference preservation

if [ $# -eq 0 ]; then
    echo "Usage: $0 <test-reference>"
    echo "Example: $0 tdd-20250718151230-E2e987"
    exit 1
fi

REFERENCE=$1
SOURCE_DIR="tests/TDDraft"
TARGET_DIR=""

# Find the test file by reference
TEST_FILE=$(grep -r "Reference: $REFERENCE" $SOURCE_DIR | cut -d: -f1)

if [ -z "$TEST_FILE" ]; then
    echo "Error: Test with reference $REFERENCE not found"
    exit 1
fi

echo "Found test: $TEST_FILE"

# Determine target directory based on test type
if grep -q "Type: feature" "$TEST_FILE"; then
    TARGET_DIR="tests/Feature"
elif grep -q "Type: unit" "$TEST_FILE"; then
    TARGET_DIR="tests/Unit"
else
    echo "Error: Cannot determine test type"
    exit 1
fi

# Extract filename
FILENAME=$(basename "$TEST_FILE")
TARGET_PATH="$TARGET_DIR/$FILENAME"

# Move file
echo "Moving $TEST_FILE to $TARGET_PATH"
mkdir -p "$TARGET_DIR"
mv "$TEST_FILE" "$TARGET_PATH"

# Update groups (remove 'tddraft' but keep reference)
sed -i "s/->group('tddraft', /->group(/g" "$TARGET_PATH"

echo "✅ Test graduated successfully!"
echo "Reference $REFERENCE preserved for tracking"
echo "Run: pest $TARGET_PATH to verify"
BASH;

echo $graduationScript . "\n\n";

echo "# Step 3: Verification workflow\n";
echo "# Test the graduated test in isolation\n";
echo "pest tests/Feature/CompleteOrderWorkflowTest.php\n\n";

echo "# Run full feature suite to ensure no conflicts\n";
echo "pest tests/Feature/\n\n";

echo "# Run complete test suite\n";
echo "pest\n\n";

echo "# Optional: Track graduated tests by reference\n";
echo "grep -r \"tdd-20250718151230-E2e987\" tests/Feature/ # Find graduated test\n\n";

// Example 11: Reference Tracking and Audit Trail
echo "11. Reference Tracking and Audit Trail\n";
echo "--------------------------------------\n";
echo "Maintain audit trail of test evolution:\n\n";

$auditExample = <<<'PHP'
// Create audit tracking script: scripts/audit-test-lineage.php
<?php

declare(strict_types=1);

// Find all tests with specific reference pattern
function findTestsByReference(string $reference): array
{
    $testDirs = ['tests/TDDraft', 'tests/Feature', 'tests/Unit'];
    $results = [];
    
    foreach ($testDirs as $dir) {
        if (!is_dir($dir)) continue;
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );
        
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') continue;
            
            $content = file_get_contents($file->getRealPath());
            if (strpos($content, "Reference: $reference") !== false) {
                $results[] = [
                    'file' => $file->getRealPath(),
                    'status' => strpos($file->getRealPath(), 'TDDraft') ? 'draft' : 'graduated',
                    'type' => $this->extractTestType($content)
                ];
            }
        }
    }
    
    return $results;
}

// Usage: php scripts/audit-test-lineage.php tdd-20250718151230-E2e987
PHP;

echo $auditExample . "\n\n";

echo "✅ Advanced TDDraft Usage Complete!\n";
echo "\nKey Advanced Features Covered:\n";
echo "• Reference-based test tracking and graduation\n";
echo "• Enterprise-level test organization strategies\n";
echo "• Automated graduation workflows with audit trails\n";
echo "• **Comprehensive status tracking and analysis**\n";
echo "• **Status-driven promotion strategies**\n";
echo "• Performance monitoring and CI/CD integration\n";
echo "• Custom helpers and advanced test patterns\n";
echo "• **Test stability analysis using historical data**\n\n";
echo "These patterns help you build robust, maintainable test suites\n";
echo "with full traceability from draft to production.\n\n";
echo "For more information, see the complete documentation in docs/\n";
