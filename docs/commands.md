# Commands

Laravel TDDraft currently provides one primary command for initializing your TDD environment.

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
pest --testsuite=tddraft

# Run all tests including TDDraft
pest --testsuite=default,tddraft
```

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