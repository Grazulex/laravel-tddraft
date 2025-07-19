<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Services\StatusTracker;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    // Clean up any existing status files
    $statusFile = base_path('tests/TDDraft/.status.json');
    if (File::exists($statusFile)) {
        File::delete($statusFile);
    }
});

afterEach(function (): void {
    // Clean up after tests
    $statusFile = base_path('tests/TDDraft/.status.json');
    if (File::exists($statusFile)) {
        File::delete($statusFile);
    }
});

it('can create status tracker instance', function (): void {
    $tracker = new StatusTracker;

    expect($tracker)->toBeInstanceOf(StatusTracker::class);
});

it('loads empty status when file does not exist', function (): void {
    $tracker = new StatusTracker;

    $status = $tracker->loadStatus();

    expect($status)->toBeArray();
    expect($status)->toBeEmpty();
});

it('can update test status', function (): void {
    $tracker = new StatusTracker;
    $reference = 'tdd-20240101120000-abc123';

    $tracker->updateTestStatus($reference, 'passed');

    $status = $tracker->getTestStatus($reference);
    expect($status)->toBe('passed');
});

it('can get all test statuses', function (): void {
    $tracker = new StatusTracker;

    $tracker->updateTestStatus('tdd-20240101120000-abc123', 'passed');
    $tracker->updateTestStatus('tdd-20240101120001-def456', 'failed');

    $allStatuses = $tracker->getAllTestStatuses();

    expect($allStatuses)->toBeArray();
    expect($allStatuses)->toHaveCount(2);
    expect($allStatuses['tdd-20240101120000-abc123'])->toBe('passed');
    expect($allStatuses['tdd-20240101120001-def456'])->toBe('failed');
});

it('can remove test from tracking', function (): void {
    $tracker = new StatusTracker;
    $reference = 'tdd-20240101120000-abc123';

    $tracker->updateTestStatus($reference, 'passed');
    expect($tracker->getTestStatus($reference))->toBe('passed');

    $tracker->removeTest($reference);
    expect($tracker->getTestStatus($reference))->toBeNull();
});

it('tracks status history when enabled', function (): void {
    config(['tddraft.status_tracking.track_history' => true]);

    $tracker = new StatusTracker;
    $reference = 'tdd-20240101120000-abc123';

    $tracker->updateTestStatus($reference, 'passed');
    $tracker->updateTestStatus($reference, 'failed');
    $tracker->updateTestStatus($reference, 'promoted');

    $data = $tracker->loadStatus();

    expect($data[$reference]['status'])->toBe('promoted');
    expect($data[$reference]['history'])->toHaveCount(2);
    expect($data[$reference]['history'][0]['status'])->toBe('passed');
    expect($data[$reference]['history'][1]['status'])->toBe('failed');
});

it('can extract reference from test data using private method', function (): void {
    $tracker = new StatusTracker;
    $reflection = new ReflectionClass($tracker);
    $method = $reflection->getMethod('extractReference');
    $method->setAccessible(true);

    // Test with groups
    $testData = [
        'groups' => ['tddraft', 'feature', 'tdd-20240101120000-abc123'],
        'name' => 'user can login',
    ];

    $reference = $method->invoke($tracker, $testData);
    expect($reference)->toBe('tdd-20240101120000-abc123');

    // Test with name
    $testData = [
        'name' => 'Test with tdd-20240101120001-def456 reference',
    ];

    $reference = $method->invoke($tracker, $testData);
    expect($reference)->toBe('tdd-20240101120001-def456');

    // Test with no reference
    $testData = [
        'name' => 'Regular test without reference',
    ];

    $reference = $method->invoke($tracker, $testData);
    expect($reference)->toBeNull();
});

it('can determine test status using private method', function (): void {
    $tracker = new StatusTracker;
    $reflection = new ReflectionClass($tracker);
    $method = $reflection->getMethod('determineStatus');
    $method->setAccessible(true);

    // Test passed
    $testData = ['status' => 'passed'];
    expect($method->invoke($tracker, $testData))->toBe('passed');

    // Test failed
    $testData = ['status' => 'failed'];
    expect($method->invoke($tracker, $testData))->toBe('failed');

    // Test error
    $testData = ['status' => 'error'];
    expect($method->invoke($tracker, $testData))->toBe('error');

    // Test unknown status
    $testData = ['status' => 'unknown_status'];
    expect($method->invoke($tracker, $testData))->toBe('unknown');
});

it('persists status data to file', function (): void {
    $tracker = new StatusTracker;
    $reference = 'tdd-20240101120000-abc123';

    $tracker->updateTestStatus($reference, 'passed');

    // Create new instance to test persistence
    $newTracker = new StatusTracker;
    $status = $newTracker->getTestStatus($reference);

    expect($status)->toBe('passed');
});

it('respects configuration for status tracking', function (): void {
    config(['tddraft.status_tracking.enabled' => false]);

    $tracker = new StatusTracker;
    $reference = 'tdd-20240101120000-abc123';

    $tracker->updateTestStatus($reference, 'passed');

    $status = $tracker->getTestStatus($reference);
    expect($status)->toBeNull();
});
