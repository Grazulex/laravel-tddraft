<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Services;

use Exception;
use Illuminate\Support\Facades\File;

final readonly class StatusTracker
{
    private string $statusFilePath;

    public function __construct()
    {
        $filePath = config('tddraft.status_tracking.file_path', 'tests/TDDraft/.status.json');
        if (! is_string($filePath)) {
            $filePath = 'tests/TDDraft/.status.json';
        }
        $this->statusFilePath = base_path($filePath);
    }

    /**
     * Load the status data from file.
     *
     * @return array<string, array{status: string, updated_at: string, history: array<array{status: string, timestamp: string}>}>
     */
    public function loadStatus(): array
    {
        if (! File::exists($this->statusFilePath)) {
            return [];
        }

        $content = File::get($this->statusFilePath);
        $data = json_decode($content, true);

        if (! is_array($data)) {
            return [];
        }

        // Ensure the data structure is correct
        $result = [];
        foreach ($data as $ref => $status) {
            if (is_string($ref) && is_array($status) &&
                isset($status['status']) && is_string($status['status']) &&
                isset($status['updated_at']) && is_string($status['updated_at']) &&
                isset($status['history']) && is_array($status['history'])) {
                // Validate history structure
                $validHistory = [];
                foreach ($status['history'] as $historyEntry) {
                    if (is_array($historyEntry) &&
                        isset($historyEntry['status']) && is_string($historyEntry['status']) &&
                        isset($historyEntry['timestamp']) && is_string($historyEntry['timestamp'])) {
                        $validHistory[] = $historyEntry;
                    }
                }

                $result[$ref] = [
                    'status' => $status['status'],
                    'updated_at' => $status['updated_at'],
                    'history' => $validHistory,
                ];
            }
        }

        return $result;
    }

    /**
     * Save status data to file.
     *
     * @param  array<string, array{status: string, updated_at: string, history: array<array{status: string, timestamp: string}>}>  $data
     */
    public function saveStatus(array $data): void
    {
        if (! config('tddraft.status_tracking.enabled', true)) {
            return;
        }

        // Ensure directory exists
        $dir = dirname($this->statusFilePath);
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $jsonContent = json_encode($data, JSON_PRETTY_PRINT);
        if ($jsonContent !== false) {
            File::put($this->statusFilePath, $jsonContent);
        }
    }

    /**
     * Update the status of a test.
     */
    public function updateTestStatus(string $reference, string $status): void
    {
        if (! config('tddraft.status_tracking.enabled', true)) {
            return;
        }

        $data = $this->loadStatus();
        $timestamp = date('c'); // ISO 8601 format string

        // Initialize test entry if not exists
        if (! isset($data[$reference])) {
            $data[$reference] = [
                'status' => $status,
                'updated_at' => $timestamp,
                'history' => [],
            ];
        }

        // Update status if different
        if ($data[$reference]['status'] !== $status) {
            // Add to history if enabled
            if (config('tddraft.status_tracking.track_history', true)) {
                $data[$reference]['history'][] = [
                    'status' => $data[$reference]['status'],
                    'timestamp' => $data[$reference]['updated_at'],
                ];

                // Limit history entries
                $maxHistoryConfig = config('tddraft.status_tracking.max_history_entries', 50);
                $maxHistory = is_int($maxHistoryConfig) ? $maxHistoryConfig : 50;
                if (count($data[$reference]['history']) > $maxHistory) {
                    $data[$reference]['history'] = array_slice($data[$reference]['history'], -$maxHistory);
                }
            }

            $data[$reference]['status'] = $status;
            $data[$reference]['updated_at'] = $timestamp;
        }

        $this->saveStatus($data);
    }

    /**
     * Get the status of a test.
     */
    public function getTestStatus(string $reference): ?string
    {
        $data = $this->loadStatus();

        return $data[$reference]['status'] ?? null;
    }

    /**
     * Get all test statuses.
     *
     * @return array<string, string>
     */
    public function getAllTestStatuses(): array
    {
        $data = $this->loadStatus();
        $statuses = [];

        foreach ($data as $reference => $info) {
            $statuses[$reference] = $info['status'];
        }

        return $statuses;
    }

    /**
     * Remove a test from status tracking.
     */
    public function removeTest(string $reference): void
    {
        $data = $this->loadStatus();

        if (isset($data[$reference])) {
            unset($data[$reference]);
            $this->saveStatus($data);
        }
    }

    /**
     * Parse test results from JSON output.
     *
     * @return array<string, string> Array of reference => status
     */
    public function parseTestResults(string $jsonOutput): array
    {
        $results = [];

        try {
            $data = json_decode($jsonOutput, true);

            if (! is_array($data) || ! isset($data['tests']) || ! is_array($data['tests'])) {
                return [];
            }

            foreach ($data['tests'] as $test) {
                if (! is_array($test)) {
                    continue;
                }

                // Extract reference from test name or groups
                $reference = $this->extractReference($test);
                if ($reference) {
                    $status = $this->determineStatus($test);
                    $results[$reference] = $status;
                }
            }
        } catch (Exception) {
            // Log error or handle as needed
            return [];
        }

        return $results;
    }

    /**
     * Extract reference from test data.
     *
     * @param  array<mixed>  $test
     */
    private function extractReference(array $test): ?string
    {
        // Try to extract from groups first
        if (isset($test['groups']) && is_array($test['groups'])) {
            foreach ($test['groups'] as $group) {
                if (is_string($group) && preg_match('/^tdd-\d{14}-[a-zA-Z0-9]{6}$/', $group)) {
                    return $group;
                }
            }
        }

        // Try to extract from test name
        if (isset($test['name']) && is_string($test['name']) && preg_match('/(tdd-\d{14}-[a-zA-Z0-9]{6})/', $test['name'], $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Determine test status from test data.
     *
     * @param  array<mixed>  $test
     */
    private function determineStatus(array $test): string
    {
        if (isset($test['status']) && is_string($test['status'])) {
            return match ($test['status']) {
                'passed' => 'passed',
                'failed' => 'failed',
                'error' => 'error',
                'skipped' => 'skipped',
                'incomplete' => 'incomplete',
                default => 'unknown',
            };
        }

        // Fallback based on other indicators
        if (isset($test['state']) && is_string($test['state'])) {
            return $test['state'] === 'passed' ? 'passed' : 'failed';
        }

        return 'unknown';
    }
}
