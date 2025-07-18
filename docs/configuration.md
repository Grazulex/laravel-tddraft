# Configuration

Laravel TDDraft can be configured through the `config/tddraft.php` file that is published when you run:

```bash
php artisan vendor:publish --tag=tddraft-config
```

## Configuration Options

### Basic Settings

```php
/**
 * Enable or disable the package functionality
 */
'enabled' => env('LARAVEL_TDDRAFT_ENABLED', true),
```

Set to `false` to completely disable the package functionality.

### Default Settings

```php
/**
 * Default settings for tddraft operations
 */
'defaults' => [
    'timeout' => 30,        // Default timeout in seconds
    'retry_attempts' => 3,  // Number of retry attempts for operations
],
```

These settings control the default behavior for package operations.

### Cache Configuration

```php
/**
 * Cache configuration
 */
'cache' => [
    'enabled' => true,              // Enable/disable caching
    'ttl' => 3600,                 // Cache TTL in seconds (1 hour)
    'key_prefix' => 'tddraft:',    // Cache key prefix
],
```

Caching can help improve performance for certain package operations.

### Logging Configuration

```php
/**
 * Logging configuration
 */
'logging' => [
    'enabled' => env('LARAVEL_TDDRAFT_LOGGING_ENABLED', false),
    'channel' => env('LARAVEL_TDDRAFT_LOG_CHANNEL', 'stack'),
    'level' => env('LARAVEL_TDDRAFT_LOG_LEVEL', 'info'),
],
```

Configure logging to track package operations and debug issues.

## Environment Variables

You can override configuration values using environment variables:

```env
# .env file
LARAVEL_TDDRAFT_ENABLED=true
LARAVEL_TDDRAFT_LOGGING_ENABLED=false
LARAVEL_TDDRAFT_LOG_CHANNEL=stack
LARAVEL_TDDRAFT_LOG_LEVEL=info
```

## Default Configuration File

The complete default configuration file:

```php
<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | LaravelTddraft Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the LaravelTddraft package.
    | You can customize these settings according to your needs.
    |
    */

    /**
     * Enable or disable the package functionality
     */
    'enabled' => env('LARAVEL_TDDRAFT_ENABLED', true),

    /**
     * Default settings for tddraft
     */
    'defaults' => [
        'timeout' => 30,
        'retry_attempts' => 3,
    ],

    /**
     * Cache configuration
     */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
        'key_prefix' => 'tddraft:',
    ],

    /**
     * Logging configuration
     */
    'logging' => [
        'enabled' => env('LARAVEL_TDDRAFT_LOGGING_ENABLED', false),
        'channel' => env('LARAVEL_TDDRAFT_LOG_CHANNEL', 'stack'),
        'level' => env('LARAVEL_TDDRAFT_LOG_LEVEL', 'info'),
    ],
];
```