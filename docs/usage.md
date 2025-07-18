# Usage Guide

This guide shows you how to use Laravel TDDraft for Test-Driven Development in your Laravel projects.

## 🔧 The Five-Command TDD Workflow

**Laravel TDDraft is built around a five-command workflow that enables true Test-Driven Development.** This structured approach is the key innovation - it provides a complete TDD cycle from experimentation to production.

### 🔄 Understanding the Flow

The five commands work together in a specific sequence that mirrors and enhances the traditional TDD Red-Green-Refactor cycle:

```
🏗️  SETUP      → 🧪  RED PHASE  → 🔄  ITERATION  → 📋  REVIEW     → 🚀  GRADUATE
tdd:init       → tdd:make      → tdd:test      → tdd:list     → tdd:promote
```

### 📋 Commands and Their Role in TDD

| Command | TDD Phase | Purpose | Example Usage |
|---------|-----------|---------|---------------|
| **`tdd:init`** | 🏗️ **Setup** | Create isolated TDD environment | `php artisan tdd:init` |
| **`tdd:make`** | 🧪 **Red Phase** | Write failing tests safely | `php artisan tdd:make "User can login"` |
| **`tdd:test`** | 🔄 **Red-Green Cycle** | Iterate on drafts without affecting CI | `php artisan tdd:test` |
| **`tdd:list`** | 📋 **Review** | Manage and inspect draft tests | `php artisan tdd:list --details` |
| **`tdd:promote`** | 🚀 **Graduate** | Move ready tests to production | `php artisan tdd:promote <reference>` |

### 🎯 Why This Workflow Is Essential

**This five-command sequence solves the core problems of practicing TDD in real projects:**

1. **Fear of breaking CI** - Draft tests run separately from main suite
2. **Experimental test clutter** - Clear separation between draft and production tests  
3. **Lost test context** - Unique references track test evolution
4. **Promotion friction** - Automated graduation from draft to CI
5. **Team coordination** - Standardized TDD workflow for all developers

## Concept

Laravel TDDraft helps you practice Test-Driven Development (TDD) by providing a separate testing environment for draft tests. This allows you to:

- Write experimental tests without affecting your main test suite
- Practice the Red-Green-Refactor cycle  
- Keep draft tests separate from production tests
- Maintain a clean test suite for CI/CD
- Track test evolution from draft to production with unique references

## TDDraft to CI Workflow

<div align="center">
  <img src="../chart.png" alt="TDDraft to CI Test Promotion Workflow" width="600">
  <p><em>The complete workflow from draft testing to CI integration</em></p>
</div>

## TDD Workflow with Laravel TDDraft

### 🔄 The Complete Five-Command Cycle

**This is the heart of Laravel TDDraft** - a structured workflow that enables real TDD practice without compromising your CI pipeline:

#### Phase 1: 🏗️ Setup (One-time)
```bash
php artisan tdd:init
```
**Purpose**: Create the isolated TDDraft environment
- Creates `tests/TDDraft/` directory
- Configures PHPUnit to separate draft from CI tests
- Sets up Pest configuration for draft test suite

#### Phase 2: 🧪 Red Phase (Feature Start)
```bash
php artisan tdd:make "User can register with valid email"
```
**Purpose**: Create failing tests that define the feature
- Generates test with unique reference for tracking
- Creates initially failing test (Red phase of TDD)
- Test runs in isolation from CI suite

#### Phase 3: 🔄 Red-Green Iteration (Development)
```bash
php artisan tdd:test --filter="User can register"
```
**Purpose**: Rapid feedback loop for TDD cycle
- Run ONLY draft tests (not CI tests)
- Quick iterations without affecting team
- Follow Red → Green → Refactor cycle safely

#### Phase 4: 📋 Review (Quality Check)
```bash
php artisan tdd:list --details
```
**Purpose**: Manage and inspect all draft tests
- View test metadata and references
- Filter by type, path, or status
- Decide which tests are ready for promotion

#### Phase 5: 🚀 Graduate (Production Ready)
```bash
php artisan tdd:promote tdd-20250718142530-Abc123
```
**Purpose**: Move mature tests to CI suite
- Automated promotion from draft to production
- Maintains reference tracking for audit trail
- Cleans up draft directory

### 🎯 Real-World Example: Complete Feature Development

Let's walk through developing a user registration feature using the five-command workflow:

```bash
# 🏗️ PHASE 1: Setup (if not already done)
php artisan tdd:init

# 🧪 PHASE 2: Define the feature with failing tests
php artisan tdd:make "User can register with valid data"
php artisan tdd:make "User registration validates email format" --type=unit
php artisan tdd:make "User registration sends welcome email"

# 🔄 PHASE 3: TDD Red-Green-Refactor cycles
# First test: User can register with valid data
php artisan tdd:test --filter="User can register with valid data"
# ❌ FAILS: Route doesn't exist
# → Add route and controller
php artisan tdd:test --filter="User can register with valid data"  
# ❌ FAILS: Controller method missing
# → Add controller method
php artisan tdd:test --filter="User can register with valid data"
# ✅ PASSES: Basic registration works

# Second test: Email validation
php artisan tdd:test --filter="email format"
# ❌ FAILS: No validation
# → Add validation rules
php artisan tdd:test --filter="email format"
# ✅ PASSES: Email validation works

# Third test: Welcome email
php artisan tdd:test --filter="welcome email"
# ❌ FAILS: No email sent
# → Add email notification
php artisan tdd:test --filter="welcome email"
# ✅ PASSES: Email functionality works

# Run all related tests
php artisan tdd:test --filter="registration"
# ✅ ALL PASS: Feature complete

# 📋 PHASE 4: Review before promotion
php artisan tdd:list
# Review all draft tests and their status

# 🚀 PHASE 5: Graduate to CI suite
php artisan tdd:promote tdd-20250718142530-Abc123  # User registration
php artisan tdd:promote tdd-20250718142531-Def456  # Email validation  
php artisan tdd:promote tdd-20250718142532-Ghi789  # Welcome email

# Verify in CI
pest  # Should include promoted tests and pass
```

### 🧠 Why This Flow Works

**The five-command workflow solves key TDD adoption barriers:**

1. **No CI Pollution**: Draft tests never interfere with team's main test suite
2. **Safe Experimentation**: You can write failing tests without breaking builds
3. **Clear Progression**: Explicit steps from idea to production-ready test
4. **Team Coordination**: Everyone follows same structured approach
5. **Audit Trail**: Unique references track test evolution for compliance

**This workflow transforms TDD from a risky practice to a safe, structured development approach.**

### 2. Create Draft Tests

Use the `tdd:make` command to create new draft tests with unique tracking:

```bash
# Create a feature test
php artisan tdd:make "User can register"

# Create a unit test  
php artisan tdd:make "Password validation" --type=unit

# Create test in a subdirectory
php artisan tdd:make "API authentication" --path=Auth/Api
```

This generates a test file with unique reference tracking:

```php
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
```

### 3. Run Draft Tests

Use the dedicated command to run only your draft tests:

```bash
# Run all draft tests
php artisan tdd:test

# Run with filtering
php artisan tdd:test --filter="user registration"

# Run with coverage
php artisan tdd:test --coverage
```

### 4. List and Manage Draft Tests

Use the `tdd:list` command to view and manage your draft tests:

```bash
# List all draft tests
php artisan tdd:list

# Show detailed information
php artisan tdd:list --details

# Filter by test type
php artisan tdd:list --type=feature
php artisan tdd:list --type=unit

# Filter by directory path
php artisan tdd:list --path=Auth
```

### 5. Follow TDD Red-Green-Refactor

1. **RED**: Test fails initially (expected)
2. **GREEN**: Implement minimal code to make test pass
3. **REFACTOR**: Clean up code while keeping tests passing

### 5. Follow TDD Red-Green-Refactor

1. **RED**: Test fails initially (expected)
2. **GREEN**: Implement minimal code to make test pass
3. **REFACTOR**: Clean up code while keeping tests passing

### 6. Graduate to Main Test Suite

When your draft test is ready for production, you have two options for promoting it:

#### Option A: Automated Promotion (Recommended)

Use the `tdd:promote` command with the unique reference:

```bash
# Basic promotion
php artisan tdd:promote tdd-20250718142530-Abc123

# Promote to specific directory
php artisan tdd:promote tdd-20250718142530-Abc123 --target=Unit

# Promote with custom file name
php artisan tdd:promote tdd-20250718142530-Abc123 --new-file=UserRegistrationTest

# Keep the original draft file
php artisan tdd:promote tdd-20250718142530-Abc123 --keep-draft
```

#### Option B: Manual Promotion

For manual control, you can still promote tests manually:

```bash
# Step 1: Move the test file
mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/Auth/UserRegistrationTest.php

# Step 2: Update the groups (remove 'tddraft', keep reference)
# Change: ->group('tddraft', 'feature', 'tdd-20250718142530-Abc123')
# To:     ->group('feature', 'tdd-20250718142530-Abc123')

# Step 3: Verify in main test suite
pest tests/Feature/Auth/UserRegistrationTest.php
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

### Primary Commands

```bash
# Run only draft tests (recommended)
php artisan tdd:test

# List all draft tests
php artisan tdd:list

# Run with options
php artisan tdd:test --filter="registration" --coverage
php artisan tdd:test --parallel --stop-on-failure
```

### Alternative Pest Commands

```bash
# Run only draft tests
pest --testsuite=tddraft

# Run only main tests (excludes drafts)
pest

# Run all tests including drafts
pest --testsuite=default,tddraft
```

### Advanced Filtering with References

```bash
# Filter by test type
php artisan tdd:test --group=feature
pest --testsuite=tddraft --group=unit

# Filter by specific reference
pest --testsuite=tddraft --group=tdd-20250718142530-Abc123

# Run specific test by name
php artisan tdd:test --filter="user can register"
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

```bash
php artisan tdd:make "Blog post can be published" --type=feature
```

```php
// tests/TDDraft/BlogPostCanBePublishedTest.php
<?php

declare(strict_types=1);

/**
 * TDDraft Test: Blog post can be published
 * 
 * Reference: tdd-20250718142530-Abc123
 * Type: feature
 * Created: 2025-07-18 14:25:30
 */

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
})->group('tddraft', 'feature', 'tdd-20250718142530-Abc123');
```

### 2. Run Test (Should Fail)

```bash
php artisan tdd:test --filter="Blog post can be published"
# Test fails because route/functionality doesn't exist
```

### 3. Implement Minimal Code

Add route, controller method, and model logic.

### 4. Run Test Again

```bash
php artisan tdd:test --filter="Blog post can be published"
# Test should now pass
```

### 5. List and Review Tests

```bash
php artisan tdd:list
# Review all your draft tests and their status
```

### 6. Promote Test

```bash
# Option A: Automated promotion
php artisan tdd:promote tdd-20250718142530-Abc123

# Option B: Manual promotion
mv tests/TDDraft/BlogPostCanBePublishedTest.php tests/Feature/BlogPostCanBePublishedTest.php
# Then remove ->group('tddraft') from the moved test
```

This workflow helps you maintain a clean separation between experimental/draft tests and your production test suite.