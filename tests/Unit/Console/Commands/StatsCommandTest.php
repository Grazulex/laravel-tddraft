<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\StatsCommand;
use Grazulex\LaravelTddraft\Services\StatusTracker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    File::delete(base_path('tests/TDDraft/.status.json'));
});

afterEach(function (): void {
    File::delete(base_path('tests/TDDraft/.status.json'));
});

it('has correct signature and description', function (): void {
    $command = new StatsCommand;

    expect($command->getName())->toBe('tdd:stats');
    expect($command->getDescription())->toBe('Show TDDraft statistics, including the average time from red to green');
});

it('reports when nothing is tracked', function (): void {
    $exitCode = Artisan::call('tdd:stats');

    expect($exitCode)->toBe(0);
    expect(Artisan::output())->toContain('No test status recorded yet.');
});

it('reports when no red to green cycle exists', function (): void {
    (new StatusTracker)->updateTestStatus('tdd-20240101120000-abc123', 'red');

    $exitCode = Artisan::call('tdd:stats');
    $output = Artisan::output();

    expect($exitCode)->toBe(0);
    expect($output)->toContain('Tracked tests: 1');
    expect($output)->toContain('No red to green cycle recorded yet.');
});

it('shows status counts and red to green statistics', function (): void {
    (new StatusTracker)->saveStatus([
        'tdd-20240101120000-abc123' => [
            'status' => 'passed',
            'updated_at' => '2024-01-01T12:10:00+00:00',
            'history' => [
                ['status' => 'red', 'timestamp' => '2024-01-01T12:00:00+00:00'],
            ],
        ],
        'tdd-20240101120001-def456' => [
            'status' => 'promoted',
            'updated_at' => '2024-01-01T14:00:00+00:00',
            'history' => [
                ['status' => 'red', 'timestamp' => '2024-01-01T12:00:00+00:00'],
                ['status' => 'passed', 'timestamp' => '2024-01-01T12:30:00+00:00'],
            ],
        ],
        'tdd-20240101120002-ghi789' => [
            'status' => 'failed',
            'updated_at' => '2024-01-01T12:00:00+00:00',
            'history' => [],
        ],
    ]);

    $exitCode = Artisan::call('tdd:stats');
    $output = Artisan::output();

    expect($exitCode)->toBe(0);
    expect($output)->toContain('Tracked tests: 3');
    expect($output)->toContain('✅ Passed');
    expect($output)->toContain('🎯 Promoted');
    expect($output)->toContain('❌ Failed');
    expect($output)->toMatch('/Cycles\s*\|\s*2/');
    expect($output)->toMatch('/Average\s*\|\s*20m/');
    expect($output)->toMatch('/Fastest\s*\|\s*10m/');
    expect($output)->toMatch('/Slowest\s*\|\s*30m/');
});
