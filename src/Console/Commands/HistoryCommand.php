<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

use Grazulex\LaravelTddraft\Services\StatusTracker;
use Grazulex\LaravelTddraft\Support\StatusFormatter;
use Illuminate\Console\Command;

final class HistoryCommand extends Command
{
    protected $signature = 'tdd:history
                           {reference : The unique reference of the draft test (e.g. tdd-20250727142530-Abc123)}';

    protected $description = 'Show the status history of a TDDraft test';

    public function handle(): int
    {
        $reference = trim($this->argument('reference'));
        if ($reference === '') {
            $this->error('❌ Please provide a test reference.');

            return self::FAILURE;
        }

        $statusTracker = new StatusTracker;
        $timeline = $statusTracker->getTestHistory($reference);

        if ($timeline === null) {
            $this->warn("🔍 No status recorded for '{$reference}'.");
            $this->comment('💡 Run it first with: php artisan tdd:test --filter="' . $reference . '"');

            return self::FAILURE;
        }

        $this->info("📜 Status history for {$reference}");
        $this->line(str_repeat('━', 80));

        $this->table(['#', 'Status', 'Changed at', 'Time in status'], $this->buildRows($timeline));

        $durations = $statusTracker->getRedToGreenDurations()[$reference] ?? [];
        if ($durations !== []) {
            $average = (int) round(array_sum($durations) / count($durations));
            $this->info('⏱️  Red to green: ' . count($durations) . ' cycle(s), average ' . StatusFormatter::duration($average));
        }

        if (! config('tddraft.status_tracking.track_history', true)) {
            $this->newLine();
            $this->comment('💡 History tracking is disabled (tddraft.status_tracking.track_history): only the current status is recorded.');
        }

        return self::SUCCESS;
    }

    /**
     * Build the table rows for a timeline, oldest first.
     *
     * @param  list<array{status: string, timestamp: string}>  $timeline
     *
     * @return list<array{int, string, string, string}>
     */
    private function buildRows(array $timeline): array
    {
        $rows = [];
        $lastIndex = count($timeline) - 1;

        foreach ($timeline as $index => $entry) {
            if ($index === $lastIndex) {
                $timeInStatus = 'current';
            } else {
                $start = strtotime($entry['timestamp']);
                $end = strtotime($timeline[$index + 1]['timestamp']);
                $timeInStatus = $start !== false && $end !== false
                    ? StatusFormatter::duration(max(0, $end - $start))
                    : '-';
            }

            $rows[] = [
                $index + 1,
                StatusFormatter::label($entry['status']),
                $entry['timestamp'],
                $timeInStatus,
            ];
        }

        return $rows;
    }
}
