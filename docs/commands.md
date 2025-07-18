# Commands

Laravel TDDraft provides several commands to help you work with Test-Driven Development and draft testing.

## tdd:init

The initialization command sets up your Laravel project for Test-Driven Development with separate draft testing.

### Usage

```bash
php artisan tdd:init
```

### What it Does

The `tdd:init` command performs several important setup tasks:

1. **Creates TDDraft Directory Structure**
   - Creates `tests/TDDraft/` directory
   - Adds `.gitkeep` file to ensure the directory is tracked by Git

2. **Configures PHPUnit**
   - Modifies `phpunit.xml` to include a separate testsuite for TDDraft tests
   - Sets `defaultTestSuite="default"` to exclude TDDraft tests from normal runs
   - Creates backup of original `phpunit.xml` before modification

3. **Configures Pest**
   - Modifies `tests/Pest.php` to exclude TDDraft directory from main test runs
   - Ensures TDDraft tests run separately from your main test suite
   - Creates backup of original `tests/Pest.php` before modification

4. **Creates Example Files**
   - Optionally creates `tests/TDDraft/ExampleDraftTest.php` with sample tests
   - Shows how to write TDDraft tests with proper annotations

### Interactive Configuration

The command will ask for confirmation before making changes:

- **PHPUnit Configuration**: Asks permission to modify `phpunit.xml`
- **Pest Configuration**: Asks permission to modify `tests/Pest.php`
- **Example Files**: Asks if you want to create example test files
- **Schema Upgrade**: If using older PHPUnit schema, offers to upgrade

### Manual Configuration

If you choose not to allow automatic configuration, the command provides manual instructions for:

- Adding testsuites to `phpunit.xml`
- Updating `tests/Pest.php` to exclude TDDraft
- Setting up the proper directory structure

### Generated PHPUnit Configuration

The command adds a testsuite configuration like this to your `phpunit.xml`:

```xml
<testsuites>
    <testsuite name="default">
        <directory suffix="Test.php">./tests/Feature</directory>
        <directory suffix="Test.php">./tests/Unit</directory>
    </testsuite>
    <testsuite name="tddraft">
        <directory suffix="Test.php">./tests/TDDraft</directory>
    </testsuite>
</testsuites>
```

### Generated Pest Configuration

The command modifies your `tests/Pest.php` to exclude TDDraft:

```php
// For newer Pest syntax:
pest()->extend(Tests\TestCase::class)->in('Feature', 'Unit');

// For older Pest syntax:
uses(Tests\TestCase::class)->in('Feature', 'Unit');
```

### Running Different Test Suites

After initialization, you can run tests separately:

```bash
# Run only main tests (excludes TDDraft)
pest

# Run only TDDraft tests  
php artisan tdd:test

# Run only TDDraft tests (alternative)
pest --testsuite=tddraft

# Run all tests including TDDraft
pest --testsuite=default,tddraft
```

## tdd:make

Create new TDDraft tests with unique reference tracking and intelligent path management.

### Usage

```bash
# Basic usage
php artisan tdd:make "User can register"

# Specify test type (feature or unit)
php artisan tdd:make "Password validation" --type=unit

# Custom path within TDDraft directory
php artisan tdd:make "API authentication" --path=Auth/Api

# Custom class name
php artisan tdd:make "Complex scenario" --class=MyCustomTest
```

### Options

- `--type=feature|unit`: Specify whether this is a feature or unit test (default: feature)
- `--path=`: Custom subdirectory path within `tests/TDDraft/`
- `--class=`: Custom class name for the test

### What it Does

1. **Generates Unique Reference**: Creates a unique tracking ID for each test
2. **Smart Path Management**: Automatically determines file location based on options
3. **Type Tracking**: Tags tests with their type (feature/unit) for filtering
4. **Template Generation**: Creates test with proper structure and comments
5. **Directory Creation**: Automatically creates subdirectories if needed

### Generated Test Structure

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
    expect(true)->toBeTrue('Replace this with your actual test implementation');
})
->group('tddraft', 'feature', 'tdd-20250718142530-Abc123')
->todo('Implement the test scenario for: user can register');
```

### Reference System

Each test gets multiple groups for flexible filtering:

- `tddraft`: Identifies this as a TDDraft test
- `feature|unit`: Indicates the test type
- `tdd-YYYYMMDDHHMMSS-RANDOM`: Unique reference for tracking

### Example Output

```
✅ TDDraft test created successfully!
📄 File: /path/to/tests/TDDraft/UserCanRegisterTest.php
🔖 Reference: tdd-20250718142530-Abc123
🏷️  Type: feature

Next steps:
  • Run your draft test: php artisan tdd:test --filter="User can register"
  • Edit the test to implement your scenario
  • When ready, promote to main suite: mv tests/TDDraft/UserCanRegisterTest.php tests/Feature/
```

## tdd:test

Run TDDraft tests with convenient options and filtering.

### Usage

```bash
# Run all TDDraft tests
php artisan tdd:test

# Filter by test name
php artisan tdd:test --filter="user registration"

# Generate coverage report
php artisan tdd:test --coverage

# Run tests in parallel
php artisan tdd:test --parallel

# Stop on first failure
php artisan tdd:test --stop-on-failure
```

### Advanced Filtering

You can also use Pest's group filtering with the generated tags:

```bash
# Run only feature tests
pest --testsuite=tddraft --group=feature

# Run only unit tests  
pest --testsuite=tddraft --group=unit

# Run specific test by reference
pest --testsuite=tddraft --group=tdd-20250718142530-Abc123
```

### Options

- `--filter=`: Filter tests by name pattern
- `--coverage`: Generate test coverage report
- `--parallel`: Run tests in parallel for faster execution
- `--stop-on-failure`: Stop execution on the first test failure

### Exit Codes

- `0`: Success - TDDraft initialized successfully
- `1`: Error - Something went wrong during initialization

### Example Output

```
🚀 Initializing Laravel TDDraft...
📂 Created /path/to/project/tests/TDDraft
📝 Created .gitkeep in TDDraft directory
📋 PHPUnit configuration needs to be updated to separate TDDraft tests.

Do you want to automatically update phpunit.xml? (yes/no) [yes]:
> yes

📋 Created backup: phpunit.xml.tddraft-backup-2024-01-15-10-30-45
✅ Updated phpunit.xml testsuites configuration with defaultTestSuite

📋 Pest configuration needs to be updated to exclude TDDraft from main test suite.

Do you want to automatically update tests/Pest.php? (yes/no) [yes]:
> yes

📋 Created backup: tests/Pest.php.tddraft-backup-2024-01-15-10-30-45
✅ Updated tests/Pest.php to exclude TDDraft directory

Do you want to create an example TDDraft test to get started? (yes/no) [yes]:
> yes

📝 Created example draft: tests/TDDraft/ExampleDraftTest.php

✅ TDDraft initialized successfully!

Next steps:
  • Create your first draft: Write tests in tests/TDDraft/
  • Run drafts only: pest --testsuite=tddraft
  • Run normal tests: pest (TDDraft excluded)
```