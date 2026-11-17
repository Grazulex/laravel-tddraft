<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Support;

final class StatusFormatter
{
    /**
     * Format a test status with an emoji label.
     */
    public static function label(string $status): string
    {
        return match ($status) {
            'red' => '🔴 Red',
            'passed' => '✅ Passed',
            'failed' => '❌ Failed',
            'error' => '💥 Error',
            'skipped' => '⏭️  Skipped',
            'incomplete' => '⏸️  Incomplete',
            'promoted' => '🎯 Promoted',
            'unknown' => '❓ Unknown',
            default => '❓ ' . ucfirst($status),
        };
    }

    /**
     * Format a duration in seconds as a short human readable string (e.g. "1d 2h 5m").
     */
    public static function duration(int $seconds): string
    {
        if ($seconds < 1) {
            return '0s';
        }

        $units = [
            'd' => 86400,
            'h' => 3600,
            'm' => 60,
            's' => 1,
        ];

        $parts = [];
        foreach ($units as $suffix => $size) {
            if ($seconds >= $size) {
                $value = intdiv($seconds, $size);
                $seconds -= $value * $size;
                $parts[] = $value . $suffix;
            }

            if (count($parts) === 3) {
                break;
            }
        }

        return implode(' ', $parts);
    }
}
