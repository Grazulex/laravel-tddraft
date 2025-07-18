# Troubleshooting

This guide helps you resolve common issues when using Laravel TDDraft.

## Installation Issues

### Pest 3 Not Found

**Error:**
```
Laravel-TDDraft requires Pest 3.0 or higher. Current version: 2.x
```

**Solution:**
Install or upgrade to Pest 3:

```bash
composer require pestphp/pest "^3.8" --dev
```

### Package Not Loading

**Error:**
```
Class 'Grazulex\LaravelTddraft\LaravelTddraftServiceProvider' not found
```

**Solution:**
1. Ensure the package is installed:
   ```bash
   composer require --dev grazulex/laravel-tddraft
   ```

2. Clear Laravel caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Initialization Issues

### PHPUnit Configuration Backup Failed

**Error:**
```
Could not create backup of phpunit.xml
```

**Solution:**
1. Check file permissions:
   ```bash
   ls -la phpunit.xml
   chmod 644 phpunit.xml
   ```

2. Ensure you have write permissions in the project root

### Pest Configuration Not Working

**Error:**
```
TDDraft tests are running with main test suite
```

**Solution:**
1. Check your `tests/Pest.php` file:
   ```php
   uses(TestCase::class)->in('Feature', 'Unit');
   ```

2. If using newer Pest syntax:
   ```php
   pest()->extend(Tests\TestCase::class)->in('Feature', 'Unit');
   ```

3. Manually exclude TDDraft:
   ```php
   // Add this line if needed
   uses(TestCase::class)->in('Feature', 'Unit')->excludeFrom('TDDraft');
   ```

### Directory Creation Failed

**Error:**
```
Failed to create tests/TDDraft directory
```

**Solution:**
1. Check permissions:
   ```bash
   ls -la tests/
   chmod 755 tests/
   ```

2. Create manually if needed:
   ```bash
   mkdir -p tests/TDDraft
   touch tests/TDDraft/.gitkeep
   ```

## Test Execution Issues

### Tests Not Running in Separate Suite

**Problem:** TDDraft tests run with main test suite

**Solution:**
1. Verify PHPUnit configuration:
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

2. Ensure `defaultTestSuite` is set:
   ```xml
   <phpunit defaultTestSuite="default" ...>
   ```

3. Run tests explicitly:
   ```bash
   pest --testsuite=tddraft
   ```

### Draft Tests Not Found

**Error:**
```
No tests found in testsuite "tddraft"
```

**Solution:**
1. Check file naming (must end with `Test.php`):
   ```
   tests/TDDraft/MyFeatureTest.php ✓
   tests/TDDraft/MyFeature.php     ✗
   ```

2. Verify test structure:
   ```php
   <?php
   
   declare(strict_types=1);
   
   it('describes behavior', function (): void {
       // Test implementation
   })->group('tddraft');
   ```

### Group Annotation Not Working

**Problem:** Tests run even when not tagged with `tddraft` group

**Solution:**
1. Ensure proper group syntax:
   ```php
   it('test description', function (): void {
       // ...
   })->group('tddraft');
   ```

2. For PHPUnit class-based tests:
   ```php
   #[Group('tddraft')]
   class MyTest extends TestCase
   {
       // ...
   }
   ```

## Configuration Issues

### Config Not Published

**Error:**
```
Configuration file not found
```

**Solution:**
1. Publish the config:
   ```bash
   php artisan vendor:publish --tag=tddraft-config
   ```

2. Verify the file exists:
   ```bash
   ls -la config/tddraft.php
   ```

### Environment Variables Not Working

**Problem:** Configuration not loading from `.env`

**Solution:**
1. Check `.env` file:
   ```env
   LARAVEL_TDDRAFT_ENABLED=true
   LARAVEL_TDDRAFT_LOGGING_ENABLED=false
   ```

2. Clear config cache:
   ```bash
   php artisan config:clear
   ```

3. Verify config loading:
   ```bash
   php artisan tinker
   >>> config('tddraft.enabled')
   ```

## Performance Issues

### Slow Test Execution

**Solution:**
1. Use database transactions:
   ```php
   uses(RefreshDatabase::class)->in('TDDraft');
   ```

2. Run tests in parallel:
   ```bash
   pest --testsuite=tddraft --parallel
   ```

3. Use factories instead of real database data when possible

### Memory Issues

**Error:**
```
Fatal error: Allowed memory size exhausted
```

**Solution:**
1. Increase PHP memory limit:
   ```bash
   php -d memory_limit=512M vendor/bin/pest --testsuite=tddraft
   ```

2. Use `--stop-on-failure` for debugging:
   ```bash
   pest --testsuite=tddraft --stop-on-failure
   ```

## Common Workflow Issues

### Tests Passing in Draft but Failing in Main Suite

**Cause:** Different test environment setup

**Solution:**
1. Check for missing dependencies in main test suite
2. Ensure proper test isolation
3. Verify factory and seeder consistency

### Draft Tests Affecting Main Tests

**Cause:** Shared test database or cache

**Solution:**
1. Ensure proper test isolation:
   ```php
   uses(RefreshDatabase::class)->in('Feature', 'Unit', 'TDDraft');
   ```

2. Clear cache between test runs:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

## Command-Specific Issues

### Five-Command Workflow Issues

#### `tdd:list` Command Issues

**Error:**
```
No TDDraft directory found. Run `php artisan tdd:init` first.
```

**Solution:**
Run the initialization command:
```bash
php artisan tdd:init
```

**Error:**
```
No TDDraft tests found in tests/TDDraft/
```

**Solution:**
Create your first draft test:
```bash
php artisan tdd:make "Your first test"
```

**Error:**
```
Could not parse draft file: tests/TDDraft/SomeTest.php
```

**Solution:**
Ensure the test file has proper TDDraft format with Reference comment:
```php
/**
 * TDDraft Test: Test Name
 * 
 * Reference: tdd-20250718142530-Abc123
 * Type: feature
 * Created: 2025-07-18 14:25:30
 */
```

#### `tdd:promote` Command Issues

**Error:**
```
No TDDraft test found with reference: tdd-20250718142530-Abc123
```

**Solution:**
1. Use `tdd:list` to find the correct reference:
   ```bash
   php artisan tdd:list
   ```
2. Copy the exact reference from the output

**Error:**
```
Target file tests/Feature/UserTest.php already exists. Overwrite?
```

**Solution:**
1. Use `--force` to overwrite without prompt:
   ```bash
   php artisan tdd:promote <reference> --force
   ```
2. Use `--new-file` to create with different name:
   ```bash
   php artisan tdd:promote <reference> --new-file=UserRegistrationTest
   ```
3. Use `--file` to append to existing file:
   ```bash
   php artisan tdd:promote <reference> --file=ExistingTest.php
   ```

**Error:**
```
Could not parse the draft test file
```

**Solution:**
Ensure your draft test file has valid TDDraft header format and proper PHP syntax.

**Error:**
```
Could not determine class name from existing file
```

**Solution:**
When using `--file` option, ensure the target file has a valid class declaration:
```php
class ExistingTest extends TestCase
{
    // ...
}
```

#### `tdd:make` Command Issues

**Error:**
```
Class name already exists
```

**Solution:**
1. Use `--class` option for custom naming:
   ```bash
   php artisan tdd:make "User registration" --class=UniqueUserRegTest
   ```

2. Use subdirectories for organization:
   ```bash
   php artisan tdd:make "User registration" --path=Auth/Registration
   ```

**Error:**
```
Directory creation failed
```

**Solution:**
1. Check write permissions:
   ```bash
   chmod 755 tests/TDDraft
   ```

2. Create path manually:
   ```bash
   mkdir -p tests/TDDraft/Auth/Registration
   ```

#### `tdd:test` Command Issues

**Error:**
```
pest: command not found
```

**Solution:**
1. Ensure Pest is installed:
   ```bash
   composer require pestphp/pest --dev
   ```

2. Use full path:
   ```bash
   ./vendor/bin/pest --testsuite=tddraft
   ```

**Error:**
```
No tests found with filter
```

**Solution:**
1. Check filter syntax:
   ```bash
   php artisan tdd:test --filter="exact test name"
   ```

2. Use group filtering instead:
   ```bash
   php artisan tdd:test --group=feature
   ```

#### Reference Tracking Issues

**Problem:** Can't find test by reference

**Solution:**
1. Search in all test directories:
   ```bash
   grep -r "tdd-20250718142530-Abc123" tests/
   ```

2. Check for graduated tests:
   ```bash
   find tests/Feature tests/Unit -name "*.php" -exec grep -l "tdd-20250718142530-Abc123" {} \;
   ```

### `tdd:init` Command Not Found

**Error:**
```
Command "tdd:init" is not defined
```

**Solution:**
1. Ensure package is installed:
   ```bash
   composer show grazulex/laravel-tddraft
   ```

2. Clear artisan command cache:
   ```bash
   php artisan clear-compiled
   php artisan optimize:clear
   ```

3. Check service provider registration in `config/app.php` (if not using auto-discovery)

### Backup Files Not Created

**Problem:** Command runs but doesn't create backups

**Solution:**
1. Check write permissions in project root
2. Verify the original files exist (`phpunit.xml`, `tests/Pest.php`)
3. Run with verbose output:
   ```bash
   php artisan tdd:init -vvv
   ```

## Debug Mode

Enable debug mode for more detailed error information:

```bash
# Enable Laravel debug mode
APP_DEBUG=true php artisan tdd:init

# Run tests with verbose output
pest --testsuite=tddraft -v

# Show detailed error traces
pest --testsuite=tddraft --debug
```

## Getting Help

If you continue to experience issues:

1. Check the [GitHub Issues](https://github.com/Grazulex/laravel-tddraft/issues)
2. Review your Laravel and Pest versions for compatibility
3. Create a minimal reproduction case
4. Open a new issue with:
   - Laravel version
   - Pest version  
   - PHP version
   - Complete error output
   - Steps to reproduce

## Logging

Enable logging to track down issues:

```php
// config/tddraft.php
'logging' => [
    'enabled' => true,
    'channel' => 'stack',
    'level' => 'debug',
],
```

Check logs in `storage/logs/laravel.log` for TDDraft-related messages.