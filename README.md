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

## ✨ Features

- 📂 Creates dedicated `tests/TDDraft/` directory for draft tests
- ⚙️ Automatically configures PHPUnit and Pest to exclude drafts from main test runs
- 🧪 Native Pest 3 support with proper test isolation
- 🔧 One-command setup with `php artisan tdd:init`
- 📋 Automatic backup of configuration files before modification
- 🎯 Built for clean TDD workflow separation

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

### Write Draft Tests

Create test files in the `tests/TDDraft/` directory:

```php
<?php

declare(strict_types=1);

// tests/TDDraft/UserCanRegisterTest.php

it('allows user registration with valid data', function (): void {
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

### Run Tests Separately

```bash
# Run only main tests (excludes TDDraft)
pest

# Run only TDDraft tests
pest --testsuite=tddraft

# Run all tests including TDDraft
pest --testsuite=default,tddraft
```

### Graduate Tests

When your draft test is ready, move it to your main test suite:

```bash
# Move the test file
mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/UserRegistrationTest.php

# Remove the tddraft group from the test
# Edit the file to remove ->group('tddraft')
```

## 📦 Available Commands

| Command | Description |
|---------|-------------|
| `tdd:init` | Initialize TDDraft environment and configuration |

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