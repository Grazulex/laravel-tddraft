# Laravel TDDraft

<div align="center">
  <img src="new_logo.png" alt="Laravel TDDraft" width="100">
  <p><strong>Set up Test-Driven Development environments in Laravel using Pest 3 with dedicated draft testing directories.</strong></p>

  [![Latest Version](https://img.shields.io/packagist/v/grazulex/laravel-tddraft)](https://packagist.org/packages/grazulex/laravel-tddraft)
  [![Total Downloads](https://img.shields.io/packagist/dt/grazulex/laravel-tddraft)](https://packagist.org/packages/grazulex/laravel-tddraft)
  [![License](https://img.shields.io/github/license/grazulex/laravel-tddraft)](LICENSE.md)
  [![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)](https://php.net)
  [![Laravel Version](https://img.shields.io/badge/laravel-%5E12.19-red)](https://laravel.com)
  [![Pest Version](https://img.shields.io/badge/pest-%5E3.8-purple)](https://pestphp.com)
  [![Code Style](https://img.shields.io/badge/code%20style-pint-orange)](https://github.com/laravel/pint)
</div>

## Overview

**Laravel TDDraft** helps you practice Test-Driven Development by providing a structured approach to draft testing in Laravel applications. It creates a separate testing environment for experimental tests that won't interfere with your main test suite or CI pipeline.

### TDDraft → CI Workflow

<div align="center">
  <img src="chart.png" alt="TDDraft to CI Test Promotion Workflow" width="600">
  <p><em>Visual representation of the TDDraft workflow and promotion to CI test suite</em></p>
</div>

The package enables a clean separation between experimental draft tests and production-ready CI tests, allowing you to practice TDD without affecting your deployment pipeline.

## ✨ Features

- 📂 Creates dedicated `tests/TDDraft/` directory for draft tests
- ⚙️ Automatically configures PHPUnit and Pest to exclude drafts from main test runs
- 🧪 Native Pest 3 support with proper test isolation
- 🔧 Complete five-command workflow: `tdd:init` → `tdd:make` → `tdd:test` → `tdd:list` → `tdd:promote`
- 📋 Automatic backup of configuration files before modification
- 🔖 Unique reference tracking for test promotion from draft to CI
- 🎯 Built for clean TDD workflow separation
- 🚀 Easy graduation path from draft tests to production test suite

## 🔧 Available Commands

Laravel TDDraft provides **five essential commands** for your complete TDD workflow:

| Command | Description | Usage |
|---------|-------------|-------|
| **`tdd:init`** | Initialize TDDraft environment and configuration | `php artisan tdd:init` |
| **`tdd:make`** | Create a new TDDraft test with unique reference tracking | `php artisan tdd:make "Test name"` |
| **`tdd:test`** | Run TDDraft tests only (alias for pest --testsuite=tddraft) | `php artisan tdd:test` |
| **`tdd:list`** | List all TDDraft tests with filtering and metadata | `php artisan tdd:list` |
| **`tdd:promote`** | Promote a TDDraft test to the CI test suite | `php artisan tdd:promote <reference>` |

### Complete Command Workflow

```bash
# 1. Initialize your TDDraft environment
php artisan tdd:init

# 2. Create draft tests with unique tracking
php artisan tdd:make "User can register"
php artisan tdd:make "Password validation" --type=unit

# 3. Run and iterate on your draft tests
php artisan tdd:test

# 4. List and manage your draft tests
php artisan tdd:list
php artisan tdd:list --details

# 5. Promote ready tests to CI suite
php artisan tdd:promote tdd-20250718142530-Abc123
```

## 🚀 Quick Start

### 1. Install the Package

```bash
composer require --dev grazulex/laravel-tddraft
```

### 2. Install Pest (Required)

> 💡 Laravel TDDraft requires Pest v3.8 or higher:

```bash
composer require pestphp/pest --dev
php artisan pest:install
```

### 3. Publish Configuration

```bash
php artisan vendor:publish --tag=tddraft-config
```

### 4. Initialize TDDraft

```bash
php artisan tdd:init
```

This command will:
- Create `tests/TDDraft/` directory structure
- Configure PHPUnit to separate TDDraft tests from main suite
- Configure Pest to exclude TDDraft from default test runs
- Optionally create example test files

## 🛠 Usage

### Create Draft Tests

Create new TDDraft tests with unique tracking:

```bash
# Create a feature test
php artisan tdd:make "User can register"

# Create a unit test  
php artisan tdd:make "Password validation" --type=unit

# Create test in a subdirectory
php artisan tdd:make "API authentication" --path=Auth/Api

# Create with custom class name
php artisan tdd:make "Complex scenario" --class=MyCustomTest
```

### Write Draft Tests

The generated test files include unique references and proper grouping:

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

### Run Tests Separately

```bash
# Run only main tests (excludes TDDraft)
pest

# Run only TDDraft tests (new shortcut!)
php artisan tdd:test

# Run TDDraft tests with options
php artisan tdd:test --filter="user registration"
php artisan tdd:test --coverage
php artisan tdd:test --parallel
php artisan tdd:test --stop-on-failure

# Filter by type
pest --testsuite=tddraft --group=feature
pest --testsuite=tddraft --group=unit

# Filter by specific reference
pest --testsuite=tddraft --group=tdd-20250718142530-Abc123

# Alternative: use pest directly
pest --testsuite=tddraft

# Run all tests including TDDraft
pest --testsuite=default,tddraft
```

### List and Manage Tests

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

Example output:
```
📋 TDDraft Tests List
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

┌──────────────────────────┬─────────────────────────────────────────┬─────────┬─────────────────────────┐
│ Reference                │ Name                                    │ Type    │ File                    │
├──────────────────────────┼─────────────────────────────────────────┼─────────┼─────────────────────────┤
│ tdd-20250718142530-Abc123│ User can register                       │ feature │ UserCanRegisterTest.php │
│ tdd-20250718141045-Def456│ Password validation                     │ unit    │ PasswordValidationTest.php│
│ tdd-20250718140012-Ghi789│ API authentication                      │ feature │ Auth/ApiAuthTest.php    │
└──────────────────────────┴─────────────────────────────────────────┴─────────┴─────────────────────────┘

📊 Total: 3 draft test(s)

💡 Tips:
  • Run specific test: php artisan tdd:test --filter="<reference>"
  • Run by type: php artisan tdd:test --filter="feature"
  • Promote draft: php artisan tdd:promote <reference>
```

### Graduate Tests

When your draft test is ready for production, you have two options for promoting it to your main test suite:

#### Option 1: Automated Promotion (Recommended)

Use the `tdd:promote` command with the unique reference for automated promotion:

```bash
# Basic promotion (promotes to Feature directory by default)
php artisan tdd:promote tdd-20250718142530-Abc123

# Promote to specific directory
php artisan tdd:promote tdd-20250718142530-Abc123 --target=Unit

# Promote with custom file name
php artisan tdd:promote tdd-20250718142530-Abc123 --new-file=UserRegistrationTest

# Append to existing test file
php artisan tdd:promote tdd-20250718142530-Abc123 --file=ExistingTest.php

# Keep the original draft file
php artisan tdd:promote tdd-20250718142530-Abc123 --keep-draft

# Force overwrite without confirmation
php artisan tdd:promote tdd-20250718142530-Abc123 --force
```

#### Option 2: Manual Promotion

For manual control, you can still promote tests manually:

```bash
# Step 1: Note the unique reference from your test file
# Example test header:
# /**
#  * TDDraft Test: User can register
#  * Reference: tdd-20250718142530-Abc123
#  * Type: feature
#  * Created: 2025-07-18 14:25:30
#  */

# Step 2: Move the test file to your main test suite
mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/Auth/UserRegistrationTest.php

# Step 3: Update the test groups (remove 'tddraft', keep reference for tracking)
# Change: ->group('tddraft', 'feature', 'tdd-20250718142530-Abc123')
# To:     ->group('feature', 'tdd-20250718142530-Abc123')

# Step 4: Run the promoted test to ensure it works in main suite
pest tests/Feature/Auth/UserRegistrationTest.php

# Step 5: Run full test suite to verify no conflicts
pest
```

### Tracking Test Lineage

The unique reference system allows you to:
- Track which tests originated from TDDraft
- Monitor test evolution from draft to production
- Maintain audit trail for compliance
- Link CI failures back to original draft intent

## 📁 Configuration

The package configuration is published to `config/tddraft.php`:

```php
return [
    'enabled' => env('LARAVEL_TDDRAFT_ENABLED', true),
    'defaults' => [
        'timeout' => 30,
        'retry_attempts' => 3,
    ],
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'key_prefix' => 'tddraft:',
    ],
    'logging' => [
        'enabled' => env('LARAVEL_TDDRAFT_LOGGING_ENABLED', false),
        'channel' => env('LARAVEL_TDDRAFT_LOG_CHANNEL', 'stack'),
        'level' => env('LARAVEL_TDDRAFT_LOG_LEVEL', 'info'),
    ],
];
```

## 🧪 Example Draft Test

```php
<?php

declare(strict_types=1);

it('should fail initially - this is normal for TDDraft', function (): void {
    // This test intentionally fails to demonstrate the TDD "red" phase
    expect(false)->toBeTrue('This draft needs implementation!');
})->group('tddraft');

it('can be promoted when ready', function (): void {
    // When this passes, you can promote it to your main test suite
    expect(true)->toBeTrue();
})->group('tddraft');
```

## 📚 Documentation

For comprehensive documentation, see the [`docs/`](docs/) directory:

- [Installation Guide](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Usage Guide](docs/usage.md)
- [Commands Reference](docs/commands.md)
- [Best Practices](docs/best-practices.md)
- [Troubleshooting](docs/troubleshooting.md)

## 🔧 Requirements

- PHP 8.3+
- Laravel 12.19+
- Pest 3.8+

## 🤝 Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## 📄 License

This package is open-source software licensed under the [MIT license](LICENSE.md).

---

<div align="center">
  Made with <span style="color: #FF9900;">❤️</span> for the <span style="color: #88C600;">Laravel</span> and <span style="color: #D2D200;">Pest</span> community
</div>