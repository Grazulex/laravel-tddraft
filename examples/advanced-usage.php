<?php

declare(strict_types=1);

/**
 * Advanced usage example for Laravel TDDraft
 *
 * This example demonstrates advanced patterns and best practices
 * for using Laravel TDDraft in complex Laravel applications with
 * the current three-command system and reference tracking.
 */
echo "Laravel TDDraft - Advanced Usage Example\n";
echo "=========================================\n\n";

echo "This example covers advanced TDDraft patterns, reference tracking,\n";
echo "and promotion workflows for complex applications.\n\n";

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

// Example 4: Configuration Management
echo "4. Configuration Management\n";
echo "---------------------------\n";
echo "Advanced configuration for different environments:\n\n";

$configExample = <<<'PHP'
// config/tddraft.php - Environment-specific settings
return [
    'enabled' => env('LARAVEL_TDDRAFT_ENABLED', app()->environment('local', 'testing')),
    
    'defaults' => [
        'timeout' => env('LARAVEL_TDDRAFT_TIMEOUT', 30),
        'retry_attempts' => env('LARAVEL_TDDRAFT_RETRIES', 3),
    ],
    
    'cache' => [
        'enabled' => !app()->environment('testing'),
        'ttl' => env('LARAVEL_TDDRAFT_CACHE_TTL', 3600),
        'key_prefix' => 'tddraft:' . app()->environment() . ':',
    ],
    
    'logging' => [
        'enabled' => env('LARAVEL_TDDRAFT_LOGGING_ENABLED', app()->environment('local')),
        'channel' => env('LARAVEL_TDDRAFT_LOG_CHANNEL', 'stack'),
        'level' => env('LARAVEL_TDDRAFT_LOG_LEVEL', app()->environment('production') ? 'error' : 'debug'),
    ],
];
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

// Example 6: Integration with CI/CD
echo "6. CI/CD Integration\n";
echo "-------------------\n";
echo "GitHub Actions workflow for TDDraft:\n\n";

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

// Example 8: Advanced Graduation Workflow
echo "8. Advanced Graduation Workflow\n";
echo "-------------------------------\n";
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

// Example 9: Reference Tracking and Audit Trail
echo "9. Reference Tracking and Audit Trail\n";
echo "-------------------------------------\n";
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
echo "• Performance monitoring and CI/CD integration\n";
echo "• Custom helpers and advanced test patterns\n\n";
echo "These patterns help you build robust, maintainable test suites\n";
echo "with full traceability from draft to production.\n\n";
echo "For more information, see the complete documentation in docs/\n";
