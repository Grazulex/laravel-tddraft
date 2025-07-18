# Usage Guide

This guide shows you how to use Laravel TDDraft for Test-Driven Development in your Laravel projects.

## Concept

Laravel TDDraft helps you practice Test-Driven Development (TDD) by providing a separate testing environment for draft tests. This allows you to:

- Write experimental tests without affecting your main test suite
- Practice the Red-Green-Refactor cycle
- Keep draft tests separate from production tests
- Maintain a clean test suite for CI/CD

## TDD Workflow

### 1. Initialize TDDraft

First, set up your TDDraft environment:

```bash
php artisan tdd:init
```

This creates the necessary directory structure and configuration.

### 2. Write Your First Draft Test

Create a new test file in `tests/TDDraft/`:

```php
<?php

declare(strict_types=1);

// tests/TDDraft/UserCanRegisterTest.php

it('allows a user to register with valid data', function (): void {
    // This test should start failing (RED phase)
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
})->group('tddraft');
```

### 3. Run Your Draft Tests

Run only your draft tests to see them fail (RED phase):

```bash
pest --testsuite=tddraft
```

### 4. Implement the Feature

Write the minimal code to make the test pass (GREEN phase):

```php
// In your controller, routes, etc.
// Implement the registration functionality
```

### 5. Run Tests Again

Verify your implementation:

```bash
pest --testsuite=tddraft
```

### 6. Refactor

Clean up your code while keeping tests passing (REFACTOR phase).

### 7. Graduate to Main Test Suite

When your draft test is solid and the feature is complete, move the test to your main test suite:

```bash
# Move the test file
mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/UserCanRegisterTest.php
```

Edit the moved test to remove the `tddraft` group:

```php
it('allows a user to register with valid data', function (): void {
    // ... test implementation
}); // Remove ->group('tddraft')
```

## Test Organization

### Draft Tests Structure

```
tests/
├── TDDraft/
│   ├── ExampleDraftTest.php
│   ├── UserCanRegisterTest.php
│   ├── ProductCanBeCreatedTest.php
│   └── ...
├── Feature/
│   └── ... (production tests)
└── Unit/
    └── ... (production tests)
```

### Naming Conventions

Use descriptive names that express the behavior being tested:

- `UserCanRegisterTest.php`
- `ProductCanBeUpdatedTest.php`
- `OrderCanBeCancelledTest.php`
- `AdminCanManageUsersTest.php`

## Writing Good Draft Tests

### Use the `tddraft` Group

Always mark your draft tests with the `tddraft` group:

```php
it('describes the behavior being tested', function (): void {
    // Test implementation
})->group('tddraft');
```

### Start with Failing Tests

Draft tests should initially fail to follow the TDD Red-Green-Refactor cycle:

```php
it('should fail initially - this is normal for TDDraft!', function (): void {
    // This test intentionally fails to demonstrate the TDD "red" phase
    expect(false)->toBeTrue('This draft needs implementation!');
})->group('tddraft');
```

### Use Descriptive Test Names

Make your test names express the intended behavior:

```php
it('prevents user registration with invalid email', function (): void {
    // ...
})->group('tddraft');

it('sends welcome email after successful registration', function (): void {
    // ...
})->group('tddraft');
```

## Running Tests

### Run Only Draft Tests

```bash
pest --testsuite=tddraft
```

### Run Only Main Tests (Excludes Drafts)

```bash
pest
# or explicitly
pest --testsuite=default
```

### Run All Tests

```bash
pest --testsuite=default,tddraft
```

### Run with Coverage

```bash
pest --testsuite=tddraft --coverage
```

## Best Practices

### 1. Keep Drafts Temporary

Draft tests are meant to be temporary. Once a feature is complete and stable, graduate the test to your main test suite.

### 2. Use Meaningful Descriptions

Write test descriptions that clearly express the behavior being tested.

### 3. One Concept Per Test

Keep your draft tests focused on testing one specific behavior or requirement.

### 4. Clean Up Regularly

Regularly review and clean up your draft tests. Remove obsolete drafts and graduate stable ones.

### 5. Don't Commit Broken Drafts

While draft tests can fail during development, avoid committing tests that fail due to incomplete implementation.

## Example TDD Session

Here's a complete example of a TDD session using Laravel TDDraft:

### 1. Create Draft Test

```php
// tests/TDDraft/BlogPostCanBePublishedTest.php
<?php

declare(strict_types=1);

it('allows publishing a draft blog post', function (): void {
    $user = User::factory()->create();
    $post = BlogPost::factory()->create([
        'user_id' => $user->id,
        'status' => 'draft'
    ]);

    $response = $this->actingAs($user)
        ->patch("/posts/{$post->id}/publish");

    $response->assertStatus(200);
    expect($post->fresh()->status)->toBe('published');
    expect($post->fresh()->published_at)->not->toBeNull();
})->group('tddraft');
```

### 2. Run Test (Should Fail)

```bash
pest --testsuite=tddraft
# Test fails because route/functionality doesn't exist
```

### 3. Implement Minimal Code

Add route, controller method, and model logic.

### 4. Run Test Again

```bash
pest --testsuite=tddraft
# Test should now pass
```

### 5. Refactor and Graduate

Clean up code, then move test to main suite:

```bash
mv tests/TDDraft/BlogPostCanBePublishedTest.php tests/Feature/BlogPostCanBePublishedTest.php
```

Remove the `->group('tddraft')` from the moved test.

This workflow helps you maintain a clean separation between experimental/draft tests and your production test suite.