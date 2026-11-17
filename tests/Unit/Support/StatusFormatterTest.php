<?php

declare(strict_types=1);

use Grazulex\LaravelTddraft\Support\StatusFormatter;

it('formats known and unknown statuses', function (): void {
    expect(StatusFormatter::label('red'))->toBe('🔴 Red');
    expect(StatusFormatter::label('passed'))->toBe('✅ Passed');
    expect(StatusFormatter::label('promoted'))->toBe('🎯 Promoted');
    expect(StatusFormatter::label('custom'))->toBe('❓ Custom');
});

it('formats durations', function (int $seconds, string $expected): void {
    expect(StatusFormatter::duration($seconds))->toBe($expected);
})->with([
    [0, '0s'],
    [-5, '0s'],
    [45, '45s'],
    [600, '10m'],
    [3725, '1h 2m 5s'],
    [90061, '1d 1h 1m'],
]);
