# Configuration

Laravel TDDraft can be configured through the `config/tddraft.php` file that is published when you run:

```bash
php artisan vendor:publish --tag=tddraft-config
```

## Configuration Options

### Status Tracking Configuration

**NEW**: Test execution status tracking and history management.

```php
/**
 * Test status tracking configuration
 *
 * Controls how test execution results are tracked and persisted.
 */
'status_tracking' => [
    // Enable or disable status tracking
    'enabled' => env('LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED', true),

    // File path where test statuses are saved (relative to Laravel base path)
    'file_path' => env('LARAVEL_TDDRAFT_STATUS_FILE', 'tests/TDDraft/.status.json'),

    // Keep history of status changes for each test
    'track_history' => env('LARAVEL_TDDRAFT_TRACK_HISTORY', true),

    // Maximum number of history entries to keep per test
    'max_history_entries' => env('LARAVEL_TDDRAFT_MAX_HISTORY', 50),
],
```

**Status Tracking Features:**
- **Automatic Tracking**: Test results are automatically tracked when you run `php artisan tdd:test`
- **Status History**: Maintains a history of status changes (passed → failed → passed, etc.)
- **JSON Storage**: Statuses are stored in `tests/TDDraft/.status.json` by default
- **Reference Linking**: Links test results to unique test references for audit trails
- **Configurable History**: Control how many historical status changes to keep

**Supported Test Statuses:**
- `passed` - Test executed successfully
- `failed` - Test failed with assertion errors
- `error` - Test had runtime errors
- `skipped` - Test was skipped
- `incomplete` - Test is marked as incomplete
- `unknown` - Status could not be determined

## Environment Variables

You can override configuration values using environment variables:

```env
# .env file

# Status tracking configuration
LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED=true
LARAVEL_TDDRAFT_STATUS_FILE=tests/TDDraft/.status.json
LARAVEL_TDDRAFT_TRACK_HISTORY=true
LARAVEL_TDDRAFT_MAX_HISTORY=50
```

## Status Tracking File Structure

When status tracking is enabled, Laravel TDDraft creates a `.status.json` file in your `tests/TDDraft/` directory with the following structure:

```json
{
  "tdd-20250718142530-Abc123": {
    "status": "passed",
    "updated_at": "2025-07-18T14:30:45+00:00",
    "history": [
      {
        "status": "failed",
        "timestamp": "2025-07-18T14:25:30+00:00"
      },
      {
        "status": "error", 
        "timestamp": "2025-07-18T14:27:15+00:00"
      }
    ]
  },
  "tdd-20250718143000-Def456": {
    "status": "failed",
    "updated_at": "2025-07-18T14:32:10+00:00", 
    "history": []
  }
}
```

This enables tracking of:
- Current test status
- Last update timestamp
- Historical status changes for audit trails
- Test evolution from draft to production

## Default Configuration File

The complete default configuration file:

```php
<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel TDDraft Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Laravel TDDraft package.
    | You can customize these settings according to your needs.
    |
    */

    /**
     * Test status tracking configuration
     *
     * Controls how test execution results are tracked and persisted.
     */
    'status_tracking' => [
        // Enable or disable status tracking
        'enabled' => env('LARAVEL_TDDRAFT_STATUS_TRACKING_ENABLED', true),

        // File path where test statuses are saved (relative to Laravel base path)
        'file_path' => env('LARAVEL_TDDRAFT_STATUS_FILE', 'tests/TDDraft/.status.json'),

        // Keep history of status changes for each test
        'track_history' => env('LARAVEL_TDDRAFT_TRACK_HISTORY', true),

        // Maximum number of history entries to keep per test
        'max_history_entries' => env('LARAVEL_TDDRAFT_MAX_HISTORY', 50),
    ],
];
```

## Using Status Tracking

Status tracking is automatically enabled and works with the `tdd:test` command:

```bash
# Run tests with automatic status tracking
php artisan tdd:test

# Status is tracked in tests/TDDraft/.status.json
# View status through tdd:list command (coming in future versions)
```

**Benefits of Status Tracking:**
- Monitor test evolution during TDD cycles
- Track which tests consistently pass vs. fail
- Audit trail for test promotion decisions
- Historical data for test reliability analysis
- Debug recurring test failures