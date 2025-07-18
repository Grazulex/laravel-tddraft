<?php

declare(strict_types=1);

/**
 * Advanced usage example for Laravel TDDraft
 *
 * This example demonstrates advanced patterns and best practices
 * for using Laravel TDDraft in complex Laravel applications.
 */
echo "Laravel TDDraft - Advanced Usage Example\n";
echo "=========================================\n\n";

echo "This example covers advanced TDDraft patterns and best practices.\n\n";

// Example 1: Complex Test Scenarios
echo "1. Complex Test Scenarios\n";
echo "-------------------------\n";
echo "Create tests/TDDraft/E2E/OrderProcessingTest.php\n\n";

$complexTest = <<<'PHP'
<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

uses(RefreshDatabase::class);

it('processes complete order workflow end-to-end', function (): void {
    // Arrange: Set up test data
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
        ->post('/orders', [
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
    $response->assertStatus(201);
    
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
})->group('tddraft');
PHP;

echo "Complex E2E test example:\n";
echo $complexTest . "\n\n";

// Example 2: Test Organization
echo "2. Test Organization Strategies\n";
echo "-------------------------------\n";
echo "Organize tests by feature modules:\n\n";

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

echo "✅ Advanced TDDraft Usage Complete!\n";
echo "These patterns help you build robust, maintainable test suites.\n";
echo "For more information, see the complete documentation in docs/\n";
