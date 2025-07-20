# Laravel TDDraft Examples

This directory contains practical examples demonstrating how to use Laravel TDDraft effectively in your Laravel applications, including the new status tracking features.

## TDDraft Workflow Overview

<div align="center">
  <img src="../chart.png" alt="TDDraft to CI Test Promotion Workflow" width="600">
  <p><em>Complete workflow from draft testing to CI integration</em></p>
</div>

## Examples Overview

### [basic-usage.php](basic-usage.php)
Demonstrates the complete five-command workflow of Laravel TDDraft:
- Package installation and setup with `tdd:init`
- Creating draft tests with `tdd:make` and unique reference tracking
- Running tests with `tdd:test` command **with automatic status tracking**
- Listing and managing tests with `tdd:list` command
- Promoting tests with `tdd:promote` command
- TDD Red-Green-Refactor cycle with status monitoring
- Automated and manual test graduation
- Visual workflow representation with chart.png

**Who should use this:** Developers new to Laravel TDDraft or TDD in general.

### [advanced-usage.php](advanced-usage.php)
Shows advanced patterns and best practices:
- Complex end-to-end test scenarios with unique references
- Test organization strategies using subdirectories
- Advanced test patterns (parameterized tests, performance testing)
- Professional test management with `tdd:list` filtering
- Enterprise promotion strategies with `tdd:promote`
- Configuration management for different environments including status tracking
- Custom test helpers and traits
- CI/CD integration with automated promotion workflows
- Performance monitoring and audit trails **using status tracking data**
- Reference-based test tracking and lineage with historical status data

**Who should use this:** Experienced developers building complex applications.

### [status-tracking-analysis.php](status-tracking-analysis.php) (NEW)
Comprehensive guide to status tracking analysis and data-driven TDD workflows:
- Understanding status tracking data structure and insights
- Custom analysis scripts for test stability assessment
- Automated promotion workflows based on status history
- CI/CD integration with status analysis and reporting
- Custom PHP classes and Laravel commands for status analysis
- Regression detection and test stability scoring
- Professional workflow patterns using status data
- Quality metrics and promotion recommendation systems

**Who should use this:** Teams implementing professional TDD workflows with data-driven test management.

## Running the Examples

These examples are educational and show code patterns rather than executable scripts. To run them:

```bash
# View basic usage patterns
php examples/basic-usage.php

# View advanced usage patterns  
php examples/advanced-usage.php

# View status tracking analysis patterns (NEW)
php examples/status-tracking-analysis.php
```

## Real-World Usage

### Getting Started

1. **Install and Initialize**
   ```bash
   composer require --dev grazulex/laravel-tddraft
   php artisan tdd:init
   ```

2. **Create Your First Draft Test**
   ```bash
   php artisan tdd:make "User can register"
   ```

3. **Run Draft Tests**
   ```bash
   php artisan tdd:test
   ```

4. **List and Manage Tests**
   ```bash
   php artisan tdd:list
   php artisan tdd:list --details
   ```

5. **Follow TDD Cycle**
   - RED: Test fails initially (expected)
   - GREEN: Implement minimal code
   - REFACTOR: Clean up while keeping tests green

6. **Promote to Main Suite**
   ```bash
   # Automated promotion (recommended)
   php artisan tdd:promote <reference>
   
   # Manual promotion (alternative)
   mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/Auth/UserRegistrationTest.php
   # Update groups: remove 'tddraft', keep reference
   ```

### Test Organization Examples

#### Feature-Based Organization
```
tests/TDDraft/
├── Authentication/
│   ├── UserRegistrationTest.php
│   ├── PasswordResetTest.php
│   └── LoginTest.php
├── Billing/
│   ├── SubscriptionTest.php
│   └── PaymentTest.php
└── Content/
    ├── BlogPostTest.php
    └── CommentTest.php
```

#### Layer-Based Organization
```
tests/TDDraft/
├── Unit/
│   ├── UserModelTest.php
│   └── OrderServiceTest.php
├── Integration/
│   ├── PaymentGatewayTest.php
│   └── EmailServiceTest.php
└── E2E/
    ├── CheckoutFlowTest.php
    └── UserOnboardingTest.php
```

## Common Patterns

### 1. List Management
```php
// List all tests with filtering and status information
php artisan tdd:list --type=feature
php artisan tdd:list --path=Auth --details

// Review tests with their current status before promotion
php artisan tdd:list --details
```

Example output with status tracking:
```
📋 TDDraft Tests List
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🔖 tdd-20250718142530-Abc123
📝 User can register with valid data  
📁 Authentication/UserRegistrationTest.php
🏷️  feature
📊 ✅ Passed
📅 2025-07-18 14:25:30
────────────────────────────────────────────────────────────

🔖 tdd-20250718141045-Def456
📝 Password validation rules
📁 Authentication/PasswordValidationTest.php
🏷️  unit
📊 ❌ Failed
📅 2025-07-18 14:10:45
────────────────────────────────────────────────────────────

📊 Total: 2 draft test(s)
```

### 2. Automated Promotion
```php
// Basic promotion
php artisan tdd:promote tdd-20250718142530-Abc123

// Advanced promotion with options
php artisan tdd:promote tdd-20250718142530-Abc123 --target=Unit --new-file=UserValidationTest --keep-draft
```

### 3. API Testing
```php
it('returns user profile via API', function (): void {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->getJson('/api/profile');
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email']
        ]);
})->group('tddraft');
```

### 4. Database Testing
```php
it('creates user record correctly', function (): void {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ];
    
    $user = User::create($userData);
    
    $this->assertDatabaseHas('users', $userData);
    expect($user->name)->toBe('John Doe');
})->group('tddraft');
```

### 5. Event Testing
```php
it('dispatches user registered event', function (): void {
    Event::fake();
    
    User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
    
    Event::assertDispatched(UserRegistered::class);
})->group('tddraft');
```

### 6. Job Testing
```php
it('queues welcome email job', function (): void {
    Queue::fake();
    
    $user = User::factory()->create();
    
    Queue::assertPushed(SendWelcomeEmail::class, function ($job) use ($user) {
        return $job->user->id === $user->id;
    });
})->group('tddraft');
```

## Best Practices from Examples

### 1. Use Descriptive Test Names
```php
// Good
it('prevents duplicate email registration', function (): void {
    // ...
})->group('tddraft');

// Avoid
it('test user creation', function (): void {
    // ...
})->group('tddraft');
```

### 2. Arrange-Act-Assert Pattern
```php
it('calculates order total correctly', function (): void {
    // Arrange
    $product = Product::factory()->create(['price' => 10.00]);
    $order = Order::factory()->create();
    
    // Act
    $order->addItem($product, 2);
    
    // Assert
    expect($order->getTotal())->toBe(20.00);
})->group('tddraft');
```

### 3. Use Factories for Test Data
```php
it('processes order with multiple items', function (): void {
    $user = User::factory()->create();
    $products = Product::factory()->count(3)->create();
    
    // Test implementation...
})->group('tddraft');
```

### 4. Test Edge Cases
```php
it('handles empty cart gracefully', function (): void {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/checkout');
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items']);
})->group('tddraft');
```

## Graduation Workflow

When your draft tests are stable and the feature is complete:

### Option 1: Automated Promotion (Recommended)

```bash
# 1. List tests to find references
php artisan tdd:list

# 2. Promote specific test
php artisan tdd:promote tdd-20250718142530-Abc123

# 3. Verify promoted test works
pest tests/Feature/UserCanRegisterTest.php

# 4. Run full test suite to ensure no conflicts
pest
```

### Option 2: Manual Promotion

```bash
# 1. Move test to appropriate directory
mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/Auth/UserRegistrationTest.php

# 2. Remove the tddraft group
# Edit the file to remove ->group('tddraft')

# 3. Run to ensure it works in main suite
pest tests/Feature/Auth/UserRegistrationTest.php

# 4. Run full test suite to ensure no conflicts
pest
```

## Configuration Examples

### Environment-Specific Settings
```bash
# .env.local (development with full tracking)
LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=true
LARAVEL_TDDRAFT_TRACK_HISTORY=true
LARAVEL_TDDRAFT_MAX_HISTORY=100

# .env.testing (CI environment with minimal tracking)  
LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=true
LARAVEL_TDDRAFT_TRACK_HISTORY=false
LARAVEL_TDDRAFT_MAX_HISTORY=10

# .env.production (disable TDDraft completely)
LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=false
```

### Status Tracking Integration
```php
// Custom script to analyze test stability using status data
<?php
$statusFile = base_path('tests/TDDraft/.status.json');
if (file_exists($statusFile)) {
    $statuses = json_decode(file_get_contents($statusFile), true);
    
    foreach ($statuses as $reference => $data) {
        $historyCount = count($data['history'] ?? []);
        $currentStatus = $data['status'] ?? 'unknown';
        
        if ($historyCount > 5 && $currentStatus === 'failed') {
            echo "⚠️  Test {$reference} has failed {$historyCount} times - needs attention\n";
        } elseif ($historyCount === 0 && $currentStatus === 'passed') {
            echo "✅ Test {$reference} stable - ready for promotion\n";
        }
    }
}
```

### Test Execution with Status Tracking
When you run `php artisan tdd:test`, the command now:
- Shows available tests before execution
- Automatically tracks results in real-time
- Saves status history to `tests/TDDraft/.status.json`
- Provides audit trails for test evolution
- Enables data-driven promotion decisions

Example status file content:
```json
{
  "tdd-20250718142530-Abc123": {
    "status": "passed",
    "updated_at": "2025-07-18T14:30:45+00:00",
    "history": [
      {
        "status": "failed",
        "timestamp": "2025-07-18T14:25:30+00:00"
      },
      {
        "status": "failed", 
        "timestamp": "2025-07-18T14:27:15+00:00"
      }
    ]
  }
}
```

### Custom Test Configuration
```php
// tests/TDDraft/CustomTestCase.php
<?php

declare(strict_types=1);

namespace Tests\TDDraft;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class CustomTestCase extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Custom setup for TDDraft tests
        $this->artisan('config:cache');
    }
}
```

## Troubleshooting Examples

See the [troubleshooting documentation](../docs/troubleshooting.md) for solutions to common issues.

## Contributing Examples

If you have useful patterns or examples to share:

1. Fork the repository
2. Add your example with clear documentation
3. Submit a pull request

See [CONTRIBUTING.md](../CONTRIBUTING.md) for detailed contribution guidelines.

## Additional Resources

- [Complete Documentation](../docs/)
- [GitHub Repository](https://github.com/Grazulex/laravel-tddraft)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [Pest Documentation](https://pestphp.com/docs)