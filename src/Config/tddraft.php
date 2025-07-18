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
