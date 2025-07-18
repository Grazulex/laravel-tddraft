# Laravel TDDraft Examples

This directory contains practical examples demonstrating how to use Laravel TDDraft effectively in your Laravel applications.

## TDDraft Workflow Overview

<div align="center">
  <img src="../chart.png" alt="TDDraft to CI Test Promotion Workflow" width="600">
  <p><em>Complete workflow from draft testing to CI integration</em></p>
</div>

## Examples Overview

### [basic-usage.php](basic-usage.php)
Demonstrates the fundamental three-command workflow of Laravel TDDraft:
- Package installation and setup with `tdd:init`
- Creating draft tests with `tdd:make` and unique reference tracking
- Running tests with `tdd:test` command
- TDD Red-Green-Refactor cycle
- Graduating tests to main suite with reference preservation
- Visual workflow representation with chart.png

**Who should use this:** Developers new to Laravel TDDraft or TDD in general.

### [advanced-usage.php](advanced-usage.php)
Shows advanced patterns and best practices:
- Complex end-to-end test scenarios with unique references
- Test organization strategies using subdirectories
- Advanced test patterns (parameterized tests, performance testing)
- Configuration management for different environments
- Custom test helpers and traits
- CI/CD integration with promotion workflows
- Performance monitoring and audit trails
- Reference-based test tracking and lineage

**Who should use this:** Experienced developers building complex applications.

## Running the Examples

These examples are educational and show code patterns rather than executable scripts. To run them:

```bash
# View basic usage patterns
php examples/basic-usage.php

# View advanced usage patterns  
php examples/advanced-usage.php
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

4. **Follow TDD Cycle**
   - RED: Test fails initially (expected)
   - GREEN: Implement minimal code
   - REFACTOR: Clean up while keeping tests green

5. **Graduate to Main Suite**
   ```bash
   # Move with reference tracking
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

### 1. API Testing
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

### 2. Database Testing
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

### 3. Event Testing
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

### 4. Job Testing
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
# .env.local
LARAVEL_TDDRAFT_ENABLED=true
LARAVEL_TDDRAFT_LOGGING_ENABLED=true

# .env.production  
LARAVEL_TDDRAFT_ENABLED=false
LARAVEL_TDDRAFT_LOGGING_ENABLED=false
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