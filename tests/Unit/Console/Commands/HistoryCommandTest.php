<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Console\Commands\HistoryCommand;
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
    $command = new HistoryCommand;

    expect($command->getName())->toBe('tdd:history');
    expect($command->getDescription())->toBe('Show the status history of a TDDraft test');
    expect($command->getDefinition()->hasArgument('reference'))->toBeTrue();
});

it('fails for a reference without recorded status', function (): void {
    $exitCode = Artisan::call('tdd:history', ['reference' => 'tdd-20240101120000-nope00']);

    expect($exitCode)->toBe(1);
    expect(Artisan::output())->toContain("No status recorded for 'tdd-20240101120000-nope00'");
});

it('shows the status timeline of a test', function (): void {
    (new StatusTracker)->saveStatus([
        'tdd-20240101120000-abc123' => [
            'status' => 'passed',
            'updated_at' => '2024-01-01T12:10:00+00:00',
            'history' => [
                ['status' => 'red', 'timestamp' => '2024-01-01T12:00:00+00:00'],
                ['status' => 'failed', 'timestamp' => '2024-01-01T12:05:00+00:00'],
            ],
        ],
    ]);

    $exitCode = Artisan::call('tdd:history', ['reference' => 'tdd-20240101120000-abc123']);
    $output = Artisan::output();

    expect($exitCode)->toBe(0);
    expect($output)->toContain('Status history for tdd-20240101120000-abc123');
    expect($output)->toContain('🔴 Red');
    expect($output)->toContain('❌ Failed');
    expect($output)->toContain('✅ Passed');
    expect($output)->toContain('2024-01-01T12:05:00+00:00');
    expect($output)->toContain('5m');
    expect($output)->toContain('current');
    expect($output)->toContain('Red to green: 1 cycle(s), average 10m');
});

it('mentions when history tracking is disabled', function (): void {
    config()->set('tddraft.status_tracking.track_history', false);

    (new StatusTracker)->updateTestStatus('tdd-20240101120000-abc123', 'red');

    $exitCode = Artisan::call('tdd:history', ['reference' => 'tdd-20240101120000-abc123']);

    expect($exitCode)->toBe(0);
    expect(Artisan::output())->toContain('History tracking is disabled');
});
