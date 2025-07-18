<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

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

        // Run the process
        $process = new Process($command, base_path());
        $process->setTty(true);

        $exitCode = $process->run(function ($type, $buffer): void {
            if (is_string($buffer)) {
                echo $buffer;
            }
        });

        if ($exitCode === 0) {
            $this->newLine();
            $this->info('✅ TDDraft tests completed successfully!');
        } else {
            $this->newLine();
            $this->warn('⚠️  Some TDDraft tests failed (this is normal during TDD red phase)');
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
}
