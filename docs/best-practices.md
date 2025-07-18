# Best Practices

This guide outlines recommended practices for using Laravel TDDraft effectively in your Test-Driven Development workflow.

## Test Organization

### Directory Structure

Keep your draft tests organized by feature or module:

```
tests/TDDraft/
├── Auth/
│   ├── UserCanRegisterTest.php
│   ├── UserCanLoginTest.php
│   └── PasswordResetTest.php
├── Blog/
│   ├── PostCanBeCreatedTest.php
│   ├── PostCanBePublishedTest.php
│   └── CommentCanBeAddedTest.php
└── Admin/
    ├── UserManagementTest.php
    └── SystemSettingsTest.php
```

### Naming Conventions

Use descriptive names that express behavior:

**Good:**
- `UserCanRegisterWithValidDataTest.php`
- `BlogPostCanBePublishedTest.php`
- `AdminCanManageUsersTest.php`

**Avoid:**
- `TestUser.php`
- `BlogTest.php`
- `Test1.php`

## Writing Draft Tests

### Start with Failing Tests

Always begin with a failing test to follow the TDD Red phase:

```php
it('allows user registration with valid data', function (): void {
    // This will fail initially - that's expected in TDD
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(201);
})->group('tddraft');
```

### Use the `tddraft` Group

Always mark draft tests with the appropriate group:

```php
it('describes the behavior', function (): void {
    // Test implementation
})->group('tddraft');
```

### Write Descriptive Test Names

Test names should read like specifications:

```php
it('prevents registration with duplicate email', function (): void {
    // ...
})->group('tddraft');

it('sends welcome email after successful registration', function (): void {
    // ...
})->group('tddraft');

it('redirects to dashboard after login', function (): void {
    // ...
})->group('tddraft');
```

### Focus on One Behavior Per Test

Keep tests focused and atomic:

```php
// Good: Tests one specific behavior
it('validates email format during registration', function (): void {
    $response = $this->post('/register', [
        'email' => 'invalid-email',
        // other data...
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
})->group('tddraft');

// Avoid: Testing multiple behaviors
it('handles registration validation', function (): void {
    // Don't test email, password, name validation all in one test
})->group('tddraft');
```

## TDD Workflow

### Follow Red-Green-Refactor

1. **Red**: Write a failing test
2. **Green**: Write minimal code to pass
3. **Refactor**: Improve code while keeping tests green

```php
// 1. RED: Start with failing test
it('calculates order total correctly', function (): void {
    $order = new Order();
    $order->addItem(new OrderItem(['price' => 10.00, 'quantity' => 2]));
    
    expect($order->getTotal())->toBe(20.00);
})->group('tddraft');

// 2. GREEN: Implement minimal solution
// 3. REFACTOR: Improve implementation
```

### Iterate in Small Steps

Make small, incremental changes:

```php
// Step 1: Test basic functionality
it('creates a blog post', function (): void {
    $post = BlogPost::create(['title' => 'Test Post']);
    expect($post)->toBeInstanceOf(BlogPost::class);
})->group('tddraft');

// Step 2: Test validation
it('requires title for blog post', function (): void {
    expect(fn() => BlogPost::create([]))
        ->toThrow(ValidationException::class);
})->group('tddraft');

// Step 3: Test additional features
it('sets default status to draft', function (): void {
    $post = BlogPost::create(['title' => 'Test Post']);
    expect($post->status)->toBe('draft');
})->group('tddraft');
```

## Test Management

### Regular Cleanup

Review and clean up draft tests regularly:

```bash
# Review current drafts
find tests/TDDraft -name "*.php" -exec echo "Reviewing: {}" \;

# Remove obsolete tests
rm tests/TDDraft/ObsoleteFeatureTest.php
```

### Graduate Stable Tests

Move completed tests to the main test suite:

```bash
# 1. Move the file
mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/Auth/UserRegistrationTest.php

# 2. Remove the tddraft group
# Edit the file to remove ->group('tddraft')

# 3. Verify it runs with main suite
pest tests/Feature/Auth/UserRegistrationTest.php
```

### Track Progress

Use comments to track development progress:

```php
it('completes user onboarding flow', function (): void {
    // TODO: Implement email verification step
    // TODO: Add profile completion
    // DONE: Basic registration
    
    $user = User::factory()->create();
    // ... test implementation
})->group('tddraft');
```

## Code Quality

### Use Factories and Seeders

Leverage Laravel's testing tools:

```php
it('allows user to update profile', function (): void {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Updated Name',
        ]);

    $response->assertStatus(200);
    expect($user->fresh()->name)->toBe('Updated Name');
})->group('tddraft');
```

### Setup and Teardown

Use appropriate setup methods:

```php
beforeEach(function (): void {
    $this->user = User::factory()->create();
})->group('tddraft');

it('allows user to create post', function (): void {
    $response = $this->actingAs($this->user)
        ->post('/posts', ['title' => 'Test Post']);
    
    $response->assertStatus(201);
})->group('tddraft');
```

### Mock External Dependencies

Mock external services and APIs:

```php
it('sends notification when order is placed', function (): void {
    Mail::fake();
    
    $order = Order::factory()->create();
    
    Mail::assertSent(OrderConfirmationMail::class);
})->group('tddraft');
```

## Performance Considerations

### Database Transactions

Use database transactions for faster test runs:

```php
uses(RefreshDatabase::class)->in('TDDraft');
```

### Selective Test Running

Run only the tests you're working on:

```bash
# Run specific test
pest tests/TDDraft/UserCanRegisterTest.php

# Run specific group
pest --group=tddraft

# Run tests matching pattern
pest --filter="user registration"
```

### Parallel Testing

For larger draft test suites:

```bash
pest --testsuite=tddraft --parallel
```

## Documentation

### Document Complex Scenarios

Add comments for complex test scenarios:

```php
it('handles concurrent order processing correctly', function (): void {
    // This test simulates two users placing orders simultaneously
    // to ensure proper inventory management and avoid overselling
    
    $product = Product::factory()->create(['stock' => 1]);
    
    // Simulate concurrent requests...
})->group('tddraft');
```

### Link to Requirements

Reference requirements or user stories:

```php
it('implements user story US-123: Guest checkout', function (): void {
    // As a guest user, I want to checkout without creating an account
    // so that I can quickly complete my purchase
    
    // Test implementation...
})->group('tddraft');
```

Following these best practices will help you maintain clean, effective draft tests that support your TDD workflow while keeping your main test suite focused on production code.