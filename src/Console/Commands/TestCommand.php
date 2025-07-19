<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

use Exception;
use Grazulex\LaravelTddraft\Services\StatusTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

final class TestCommand extends Command
{
    protected $signature = 'tdd:test 
                           {--filter= : Filter tests by name}
                           {--coverage : Generate coverage report}
                           {--parallel : Run tests in parallel}
                           {--stop-on-failure : Stop on first failure}';

    protected $description = 'Run TDDraft tests only (alias for pest --testsuite=tddraft)';

    public function handle(): int
    {
        $this->info('🧪 Running TDDraft tests...');

        // Show available tests first
        $this->showAvailableTests();

        $this->newLine();

        // Build the pest command
        $command = [
            './vendor/bin/pest',
            '--testsuite=tddraft',
        ];

        // Add JSON output for status tracking
        $statusTracker = new StatusTracker;
        $tempJsonFile = tempnam(sys_get_temp_dir(), 'tddraft_results');

        if (config('tddraft.status_tracking.enabled', true) && $tempJsonFile !== false) {
            $command[] = '--log-junit=' . $tempJsonFile;
        }

        // Add optional arguments
        if ($this->option('filter')) {
            $command[] = '--filter=' . $this->option('filter');
        }

        if ($this->option('coverage')) {
            $command[] = '--coverage';
        }

        if ($this->option('parallel')) {
            $command[] = '--parallel';
        }

        if ($this->option('stop-on-failure')) {
            $command[] = '--stop-on-failure';
        }

        // Check if pest is available
        if (! file_exists(base_path('vendor/bin/pest'))) {
            $this->error('❌ Pest is not installed. Please run: composer require pestphp/pest --dev');

            return self::FAILURE;
        }

        // Run the process and capture output
        $process = new Process($command, base_path());
        $process->setTty(true);
        $output = '';

        $exitCode = $process->run(function ($type, $buffer) use (&$output): void {
            if (is_string($buffer)) {
                echo $buffer;
                $output .= $buffer;
            }
        });

        // Update test statuses based on results
        $this->updateTestStatuses($output, $tempJsonFile !== false ? $tempJsonFile : null, $statusTracker);

        if ($exitCode === 0) {
            $this->newLine();
            $this->info('✅ TDDraft tests completed successfully!');
        } else {
            $this->newLine();
            $this->warn('⚠️  Some TDDraft tests failed (this is normal during TDD red phase)');
        }

        // Clean up temp file
        if ($tempJsonFile && file_exists($tempJsonFile)) {
            unlink($tempJsonFile);
        }

        return $exitCode;
    }

    /**
     * Show available TDDraft tests with their references.
     */
    private function showAvailableTests(): void
    {
        $tddraftPath = base_path('tests/TDDraft');

        if (! File::exists($tddraftPath)) {
            $this->warn('📝 No TDDraft directory found. Run `php artisan tdd:init` first.');

            return;
        }

        $files = File::allFiles($tddraftPath);
        if (empty($files)) {
            $this->warn('📝 No TDDraft tests found. Create one with: php artisan tdd:make "Test name"');

            return;
        }

        $this->comment('📋 Found TDDraft tests:');

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getPathname());
            $reference = '';
            $name = '';

            // Extract reference and name
            if (preg_match('/Reference:\s*(tdd-\d{14}-[a-zA-Z0-9]{6})/', $content, $matches)) {
                $reference = $matches[1];
            }
            if (preg_match('/TDDraft Test:\s*(.+)/', $content, $matches)) {
                $name = trim($matches[1]);
            }

            if ($reference !== '' && $reference !== '0') {
                $fileName = $file->getFilename();
                $this->line("  🔖 <fg=cyan>{$reference}</> - <fg=white>{$name}</> (<fg=gray>{$fileName}</>)");
            }
        }
    }

    /**
     * Update test statuses based on test results.
     */
    private function updateTestStatuses(string $output, ?string $jsonFile, StatusTracker $statusTracker): void
    {
        if (! config('tddraft.status_tracking.enabled', true)) {
            return;
        }

        // Parse test results from output
        $this->parseTestResultsFromOutput($output, $statusTracker);

        // If we have a JUnit XML file, parse that too
        if ($jsonFile && file_exists($jsonFile)) {
            $this->parseJUnitResults($jsonFile, $statusTracker);
        }
    }

    /**
     * Parse test results from console output.
     */
    private function parseTestResultsFromOutput(string $output, StatusTracker $statusTracker): void
    {
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            // Look for test result lines
            if (preg_match('/^\s*[✓⨯]\s+(.+?)(?:\s+\d+\.\d+s)?$/', $line, $matches)) {
                $testName = trim($matches[1]);

                // Extract reference from test name if possible
                if (preg_match('/(tdd-\d{14}-[a-zA-Z0-9]{6})/', $testName, $refMatches)) {
                    $reference = $refMatches[1];
                    $status = str_contains($line, '✓') ? 'passed' : 'failed';
                    $statusTracker->updateTestStatus($reference, $status);
                }
            }
        }
    }

    /**
     * Parse JUnit XML results.
     */
    private function parseJUnitResults(string $xmlFile, StatusTracker $statusTracker): void
    {
        try {
            $xml = simplexml_load_file($xmlFile);
            if ($xml === false) {
                return;
            }

            $testcases = $xml->xpath('//testcase');
            if (! $testcases) {
                return;
            }

            foreach ($testcases as $testcase) {
                $name = (string) $testcase['name'];

                // Extract reference from test name
                if (preg_match('/(tdd-\d{14}-[a-zA-Z0-9]{6})/', $name, $matches)) {
                    $reference = $matches[1];

                    // Determine status
                    $status = 'passed';
                    if ($testcase->failure || $testcase->error) {
                        $status = $testcase->error ? 'error' : 'failed';
                    } elseif ($testcase->skipped) {
                        $status = 'skipped';
                    }

                    $statusTracker->updateTestStatus($reference, $status);
                }
            }
        } catch (Exception) {
            // Silent fail - we'll have console output parsing as fallback
        }
    }
}
