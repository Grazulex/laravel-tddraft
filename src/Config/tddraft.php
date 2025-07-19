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
