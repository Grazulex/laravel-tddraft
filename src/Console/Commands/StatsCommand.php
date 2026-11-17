<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

use Grazulex\LaravelTddraft\Services\StatusTracker;
use Grazulex\LaravelTddraft\Support\StatusFormatter;
use Illuminate\Console\Command;

final class StatsCommand extends Command
{
    protected $signature = 'tdd:stats';

    protected $description = 'Show TDDraft statistics, including the average time from red to green';

    public function handle(): int
    {
        $statusTracker = new StatusTracker;
        $statuses = $statusTracker->getAllTestStatuses();

        $this->info('📈 TDDraft Statistics');
        $this->line(str_repeat('━', 80));

        if ($statuses === []) {
            $this->warn('📝 No test status recorded yet.');
            $this->comment('💡 Run your drafts with: php artisan tdd:test');

            return self::SUCCESS;
        }

        $this->info('📊 Tracked tests: ' . count($statuses));
        $this->table(['Status', 'Tests'], $this->buildStatusRows($statuses));

        $durations = array_merge(...array_values($statusTracker->getRedToGreenDurations()));

        if ($durations === []) {
            $this->comment('⏱️  No red to green cycle recorded yet.');

            return self::SUCCESS;
        }

        $average = (int) round(array_sum($durations) / count($durations));

        $this->table(['Red to green', 'Value'], [
            ['Cycles', (string) count($durations)],
            ['Average', StatusFormatter::duration($average)],
            ['Fastest', StatusFormatter::duration(min($durations))],
            ['Slowest', StatusFormatter::duration(max($durations))],
        ]);

        return self::SUCCESS;
    }

    /**
     * Count tests per current status.
     *
     * @param  array<string, string>  $statuses
     *
     * @return list<array{string, int}>
     */
    private function buildStatusRows(array $statuses): array
    {
        $counts = array_count_values($statuses);
        arsort($counts);

        $rows = [];
        foreach ($counts as $status => $count) {
            $rows[] = [StatusFormatter::label((string) $status), $count];
        }

        return $rows;
    }
}
