# Installation

Laravel TDDraft is a Laravel package that helps you set up separate TDD testing environments using Pest 3.

## Requirements

- PHP 8.3 or higher
- Laravel 12.19 or higher  
- Pest 3.8 or higher

## Installation Steps

### 1. Install the Package

```bash
composer require --dev grazulex/laravel-tddraft
```

### 2. Install Pest (if not already installed)

Laravel TDDraft requires Pest v3 to function properly:

```bash
composer require pestphp/pest --dev
php artisan pest:install
```

### 3. Publish Configuration

```bash
php artisan vendor:publish --tag=tddraft-config
```

This will create a `config/tddraft.php` configuration file.

### 4. Initialize TDDraft

Run the initialization command to set up your TDDraft environment:

```bash
php artisan tdd:init
```

This command will:
- Create a `tests/TDDraft/` directory for your draft tests
- Configure PHPUnit to exclude TDDraft tests from your main test suite
- Configure Pest to exclude TDDraft tests from your main test suite
- Optionally create an example draft test file

## What Gets Created

After running `tdd:init`, you'll have:

```
tests/
├── TDDraft/
│   ├── .gitkeep
│   └── ExampleDraftTest.php (optional)
├── Pest.php (modified to exclude TDDraft)
└── ...

config/
└── tddraft.php

phpunit.xml (modified to include TDDraft testsuite)
```

## Configuration

The published configuration file (`config/tddraft.php`) contains settings for:

- Package enablement
- Default timeouts and retry attempts
- Caching configuration
- Logging configuration

See [Configuration](configuration.md) for detailed configuration options.

## Next Steps - The Five Essential Commands

After installation, you'll have access to **five powerful commands** for TDD workflow:

| Command | Purpose |
|---------|---------|
| **`tdd:init`** | ✅ Already used - initializes TDDraft environment |
| **`tdd:make`** | Create new draft tests with unique tracking |
| **`tdd:test`** | Run your draft tests separately from main suite |
| **`tdd:list`** | List and manage all your draft tests |
| **`tdd:promote`** | Graduate ready tests to your CI test suite |

### Quick Start with Commands

```bash
# Create your first draft test
php artisan tdd:make "User can register"

# Run it
php artisan tdd:test

# List your drafts
php artisan tdd:list

# When ready, promote to CI
php artisan tdd:promote <test-reference>
```

See [Usage Guide](usage.md) for detailed examples and [Commands Reference](commands.md) for complete documentation.