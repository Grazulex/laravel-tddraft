# Best Practices

This guide outlines recommended practices for using Laravel TDDraft effectively in your Test-Driven Development workflow with the complete five-command system.

## TDDraft Command Workflow

### Command Sequence Best Practices

Follow the five-command workflow for optimal TDD experience:

```bash
# 1. Initialize once per project
php artisan tdd:init

# 2. Create draft tests with unique tracking
php artisan tdd:make "Feature description" --type=feature --path=Domain/Feature

# 3. Run and iterate
php artisan tdd:test --filter="feature description"

# 4. List and manage your tests
php artisan tdd:list --details

# 5. Promote when ready
php artisan tdd:promote <reference> --target=Feature
```

### Using tdd:make Effectively

Create well-organized tests with proper metadata:

```bash
# Feature tests with domain organization
php artisan tdd:make "User can complete checkout" --path=Ecommerce/Checkout

# Unit tests for specific components  
php artisan tdd:make "Payment service validates credit cards" --type=unit --path=Services

# API integration tests
php artisan tdd:make "Third party payment gateway integration" --path=Integrations/Payment
```

### Leveraging tdd:test Options

Use command options for efficient testing:

```bash
# Focus on current feature
php artisan tdd:test --filter="checkout"

# Run with performance monitoring
php artisan tdd:test --coverage --parallel

# Debug failing tests
php artisan tdd:test --stop-on-failure
```

### Status Tracking Best Practices (NEW)

Leverage the automatic status tracking for better test management:

**Monitor Test Evolution:**
```bash
# Check status file for test stability patterns
cat tests/TDDraft/.status.json | jq '.[] | select(.history | length > 3)'

# Review tests with consistent failures
grep -l "failed" tests/TDDraft/.status.json
```

**Use Status Data for Promotion Decisions:**
```bash
# Find stable tests (no history changes = consistently passing)
php artisan tdd:list --details  # Look for tests without status changes
```

**Environment-Specific Tracking:**
```bash
# Development: Full tracking
LARAVEL_TDDRAFT_TRACK_HISTORY=true
LARAVEL_TDDRAFT_MAX_HISTORY=100

# CI: Minimal tracking  
LARAVEL_TDDRAFT_TRACK_HISTORY=false
LARAVEL_TDDRAFT_MAX_HISTORY=10
```

### Using tdd:list for Test Management

Organize and review your draft tests effectively:

```bash
# Review all tests before end-of-sprint
php artisan tdd:list --details

# Focus on specific areas
php artisan tdd:list --path=Ecommerce --type=feature

# Find tests ready for promotion (those with stable passing status)
php artisan tdd:list --type=feature | grep "passing"
```

### Best Practices for tdd:promote

Promote tests systematically and safely:

```bash
# Always test before promoting
php artisan tdd:test --filter="<reference>"

# Use descriptive file names
php artisan tdd:promote <reference> --new-file=UserCheckoutWorkflowTest

# Keep drafts during uncertain periods
php artisan tdd:promote <reference> --keep-draft

# Verify after promotion
pest tests/Feature/UserCheckoutWorkflowTest.php
```

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
})->group('tddraft', 'feature', 'tdd-20250718142530-Abc123');
```

### Use the `tddraft` Group with References

Always mark draft tests with the appropriate groups including the unique reference for tracking:

```php
it('describes the behavior', function (): void {
    // Test implementation
})->group('tddraft', 'feature', 'tdd-20250718142530-Abc123');
```

**Best Practice:** Use `php artisan tdd:make` to automatically generate tests with proper grouping and unique references. Manual test creation should follow the same pattern.

### Write Descriptive Test Names

Test names should read like specifications:

```php
it('prevents registration with duplicate email', function (): void {
    // ...
})->group('tddraft', 'feature', 'tdd-20250718142530-Abc123');

it('sends welcome email after successful registration', function (): void {
    // ...
})->group('tddraft', 'feature', 'tdd-20250718142531-Def456');

it('redirects to dashboard after login', function (): void {
    // ...
})->group('tddraft', 'feature', 'tdd-20250718142532-Ghi789');
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
})->group('tddraft', 'feature', 'tdd-20250718142534-Mno345');

// Avoid: Testing multiple behaviors
it('handles registration validation', function (): void {
    // Don't test email, password, name validation all in one test
})->group('tddraft', 'feature', 'tdd-20250718142535-Pqr678');
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

Move completed tests to the main test suite with reference preservation:

```bash
# Method 1: Manual graduation with reference tracking
# 1. Note the unique reference from test header
grep -n "Reference:" tests/TDDraft/UserCanRegisterTest.php
# Reference: tdd-20250718142530-Abc123

# 2. Move the file to appropriate location
mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/Auth/UserRegistrationTest.php

# 3. Update groups (remove 'tddraft', keep reference)
sed -i "s/->group('tddraft', 'feature', 'tdd-20250718142530-Abc123')/->group('feature', 'tdd-20250718142530-Abc123')/g" tests/Feature/Auth/UserRegistrationTest.php

# 4. Verify in main test suite
pest tests/Feature/Auth/UserRegistrationTest.php
```

#### Automated Graduation Script

Create a script for systematic promotion:

```bash
#!/bin/bash
# scripts/graduate-test.sh
# Usage: ./scripts/graduate-test.sh tdd-20250718142530-Abc123

REFERENCE=$1
TEST_FILE=$(grep -r "Reference: $REFERENCE" tests/TDDraft | cut -d: -f1)

if [ -z "$TEST_FILE" ]; then
    echo "Error: Test with reference $REFERENCE not found"
    exit 1
fi

# Determine target directory based on test type
if grep -q "Type: feature" "$TEST_FILE"; then
    TARGET_DIR="tests/Feature"
elif grep -q "Type: unit" "$TEST_FILE"; then
    TARGET_DIR="tests/Unit"
fi

FILENAME=$(basename "$TEST_FILE")
TARGET_PATH="$TARGET_DIR/$FILENAME"

# Move and update
mv "$TEST_FILE" "$TARGET_PATH"
sed -i "s/->group('tddraft', /->group(/g" "$TARGET_PATH"

echo "✅ Test graduated: $TARGET_PATH"
echo "Reference $REFERENCE preserved for tracking"
```

### Reference-Based Test Tracking

Use unique references for audit trails:

```php
// Generated by tdd:make - never modify the reference
/**
 * TDDraft Test: User can register
 * 
 * Reference: tdd-20250718142530-Abc123  // ← This tracks test lineage
 * Type: feature
 * Created: 2025-07-18 14:25:30
 */

it('user can register', function (): void {
    // Test implementation
})
->group('tddraft', 'feature', 'tdd-20250718142530-Abc123') // ← Reference in groups too
->todo('Implement user registration workflow');
```

#### Tracking Graduated Tests

Maintain audit trail after graduation:

```bash
# Find all tests with specific reference (draft or graduated)
grep -r "tdd-20250718142530-Abc123" tests/

# Track graduation history
git log --oneline --follow tests/Feature/Auth/UserRegistrationTest.php
```

### Test Promotion Criteria

#### Ready for Graduation Checklist

Before promoting a test to the main suite:

- [ ] Test passes consistently
- [ ] Feature implementation is complete
- [ ] Test covers edge cases adequately
- [ ] No TODO items remain in test
- [ ] Code review completed
- [ ] Test runs in under reasonable time (<1s for unit, <5s for feature)

#### Graduation Workflow

```bash
# 1. Run final check on draft test
php artisan tdd:test --group=tdd-20250718142530-Abc123

# 2. Graduate using reference
./scripts/graduate-test.sh tdd-20250718142530-Abc123

# 3. Verify in main suite
pest tests/Feature/Auth/UserRegistrationTest.php

# 4. Run full suite to ensure no conflicts
pest

# 5. Commit graduation
git add tests/Feature/Auth/UserRegistrationTest.php
git commit -m "Graduate test tdd-20250718142530-Abc123 to main suite

- Moved from TDDraft to Feature/Auth
- Preserved reference for audit trail
- Verified no test conflicts"
```

## Test Promotion Strategies

### By Feature Completion

Graduate tests when features are fully implemented:

```bash
# Graduate all tests for a completed feature
grep -r "checkout" tests/TDDraft/ | cut -d: -f1 | while read file; do
    REFERENCE=$(grep "Reference:" "$file" | sed 's/.*Reference: //' | awk '{print $1}')
    ./scripts/graduate-test.sh "$REFERENCE"
done
```

### By Sprint/Release Cycle

Promote stable tests at end of development cycles:

```bash
# Find tests created in current sprint
find tests/TDDraft -name "*.php" -newermt "2 weeks ago" -exec grep -l "Reference:" {} \;

# Graduate tests that have been stable for 1 week
find tests/TDDraft -name "*.php" -not -newermt "1 week ago" -exec grep -l "Reference:" {} \;
```

### By Test Stability

Track test success rates before graduation:

```bash
# Run tests multiple times to check stability
for i in {1..10}; do
    php artisan tdd:test --group=tdd-20250718142530-Abc123 --stop-on-failure
done
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