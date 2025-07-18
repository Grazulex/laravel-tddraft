<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class ListCommand extends Command
{
    protected $signature = 'tdd:list 
                           {--type= : Filter by test type (feature|unit)}
                           {--path= : Filter by directory path}
                           {--details : Show detailed information}';

    protected $description = 'List all TDDraft tests with their references and metadata';

    public function handle(): int
    {
        $tddraftPath = base_path('tests/TDDraft');

        if (! File::exists($tddraftPath)) {
            $this->warn('🔍 No TDDraft directory found. Run `php artisan tdd:init` first.');

            return self::FAILURE;
        }

        $this->info('📋 TDDraft Tests List');
        $this->line(str_repeat('━', 80));

        $drafts = $this->collectDrafts($tddraftPath);

        if ($drafts === []) {
            $this->warn('📝 No TDDraft tests found in tests/TDDraft/');
            $this->newLine();
            $this->info('💡 Create your first draft test with: php artisan tdd:make "Your test name"');

            return self::SUCCESS;
        }

        // Apply filters
        $filteredDrafts = $this->applyFilters($drafts);

        if ($filteredDrafts === []) {
            $this->warn('🔍 No TDDraft tests match your filters.');

            return self::SUCCESS;
        }

        // Display drafts
        $this->displayDrafts($filteredDrafts);

        $this->newLine();
        $this->info('📊 Total: ' . count($filteredDrafts) . ' draft test(s)');
        $this->newLine();
        $this->comment('💡 Tips:');
        $this->comment('  • Run specific test: php artisan tdd:test --filter="<reference>"');
        $this->comment('  • Run by type: php artisan tdd:test --filter="feature"');
        $this->comment('  • Promote draft: php artisan tdd:promote <reference>');

        return self::SUCCESS;
    }

    /**
     * Collect all draft tests with their metadata.
     *
     * @return array<string, array{reference: string, name: string, type: string, file: string, path: string, created: ?string}>
     */
    private function collectDrafts(string $tddraftPath): array
    {
        $drafts = [];
        $files = File::allFiles($tddraftPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $draftInfo = $this->parseDraftFile($file->getPathname());
                if ($draftInfo) {
                    $drafts[$draftInfo['reference']] = $draftInfo;
                }
            }
        }

        // Sort by creation date (newest first)
        uasort($drafts, fn($a, $b): int => strcmp((string) $b['reference'], (string) $a['reference']));

        return $drafts;
    }

    /**
     * Parse a draft file to extract metadata.
     *
     * @return array{reference: string, name: string, type: string, file: string, path: string, created: ?string}|null
     */
    private function parseDraftFile(string $filePath): ?array
    {
        $content = File::get($filePath);
        $relativePath = str_replace(base_path('tests/TDDraft/'), '', $filePath);

        // Extract metadata
        $reference = '';
        $name = '';
        $type = 'feature'; // default
        $created = null;

        // Extract reference
        if (preg_match('/Reference:\s*(tdd-\d{14}-[a-zA-Z0-9]{6})/', $content, $matches)) {
            $reference = $matches[1];
        }

        // Extract test name
        if (preg_match('/TDDraft Test:\s*(.+)/', $content, $matches)) {
            $name = trim($matches[1]);
        }

        // Extract type
        if (preg_match('/Type:\s*(\w+)/', $content, $matches)) {
            $type = $matches[1];
        }

        // Extract creation date
        if (preg_match('/Created:\s*(.+)/', $content, $matches)) {
            $created = trim($matches[1]);
        }

        // If no reference found, try to extract from group
        if (($reference === '' || $reference === '0') && preg_match("/->group\([^)]*'(tdd-\d{14}-[a-zA-Z0-9]{6})'/", $content, $matches)) {
            $reference = $matches[1];
        }

        if ($reference === '' || $reference === '0') {
            return null; // Not a valid TDDraft file
        }

        return [
            'reference' => $reference,
            'name' => $name ?: basename($filePath, '.php'),
            'type' => $type,
            'file' => basename($filePath),
            'path' => $relativePath,
            'created' => $created,
        ];
    }

    /**
     * Apply command-line filters to the drafts.
     *
     * @param  array<string, array{reference: string, name: string, type: string, file: string, path: string, created: ?string}>  $drafts
     *
     * @return array<string, array{reference: string, name: string, type: string, file: string, path: string, created: ?string}>
     */
    private function applyFilters(array $drafts): array
    {
        $typeFilter = $this->option('type');
        $pathFilter = $this->option('path');

        if ($typeFilter) {
            $drafts = array_filter($drafts, fn($draft): bool => strtolower((string) $draft['type']) === strtolower($typeFilter));
        }

        if ($pathFilter) {
            return array_filter($drafts, fn($draft): bool => str_contains(strtolower((string) $draft['path']), strtolower($pathFilter)));
        }

        return $drafts;
    }

    /**
     * Display the draft tests in a formatted table.
     *
     * @param  array<string, array{reference: string, name: string, type: string, file: string, path: string, created: ?string}>  $drafts
     */
    private function displayDrafts(array $drafts): void
    {
        if ($this->option('details')) {
            $this->displayDetailedList($drafts);
        } else {
            $this->displayCompactList($drafts);
        }
    }

    /**
     * Display drafts in detailed format.
     *
     * @param  array<string, array{reference: string, name: string, type: string, file: string, path: string, created: ?string}>  $drafts
     */
    private function displayDetailedList(array $drafts): void
    {
        foreach ($drafts as $draft) {
            $this->newLine();
            $this->line("🔖 <fg=cyan>{$draft['reference']}</>");
            $this->line("📝 <fg=white>{$draft['name']}</>");
            $this->line("📁 <fg=gray>{$draft['path']}</>");
            $this->line("🏷️  <fg=yellow>{$draft['type']}</>");
            if ($draft['created']) {
                $this->line("📅 <fg=gray>{$draft['created']}</>");
            }
            $this->line(str_repeat('─', 60));
        }
    }

    /**
     * Display drafts in compact table format.
     *
     * @param  array<string, array{reference: string, name: string, type: string, file: string, path: string, created: ?string}>  $drafts
     */
    private function displayCompactList(array $drafts): void
    {
        $headers = ['Reference', 'Name', 'Type', 'File'];
        $rows = [];

        foreach ($drafts as $draft) {
            $rows[] = [
                $draft['reference'],
                $this->truncate($draft['name'], 40),
                $draft['type'],
                $draft['file'],
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Truncate a string to a maximum length.
     */
    private function truncate(string $text, int $maxLength): string
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        return substr($text, 0, $maxLength - 3) . '...';
    }
}
