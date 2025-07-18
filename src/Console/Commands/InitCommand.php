<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class InitCommand extends Command
{
    protected $signature = 'tdd:init';

    protected $description = 'Initialize TDDraft: configure PHPUnit, Pest, and create necessary directories';

    public function handle(): int
    {
        $this->info('🚀 Initializing Laravel TDDraft...');

        // Create TDDraft directory
        $this->createTddraftDirectory();

        // Configure PHPUnit
        $this->configurePhpUnit();

        // Configure Pest
        $this->configurePest();

        // Create example files
        $this->createExampleFiles();

        $this->newLine();
        $this->info('✅ TDDraft initialized successfully!');
        $this->newLine();
        $this->comment('Next steps:');
        $this->line('  • Create your first draft: php artisan tdd:make "My first test"');
        $this->line('  • Run drafts only: php artisan tdd:test');
        $this->line('  • Run normal tests: pest (TDDraft excluded)');

        return self::SUCCESS;
    }

    private function createTddraftDirectory(): void
    {
        $tddraftPath = base_path('tests/TDDraft');

        if (! File::exists($tddraftPath)) {
            File::makeDirectory($tddraftPath, 0755, true);
            $this->info("📂 Created {$tddraftPath}");
        } else {
            $this->comment("📂 Directory {$tddraftPath} already exists");
        }

        // Create .gitkeep to ensure directory is tracked
        $gitkeepPath = $tddraftPath . '/.gitkeep';
        if (! File::exists($gitkeepPath)) {
            File::put($gitkeepPath, '');
            $this->info('📝 Created .gitkeep in TDDraft directory');
        }
    }

    private function configurePhpUnit(): void
    {
        $phpunitPath = base_path('phpunit.xml');

        if (! File::exists($phpunitPath)) {
            $this->warn('⚠️  phpunit.xml not found, skipping PHPUnit configuration');

            return;
        }

        $content = File::get($phpunitPath);

        // Check if testsuites already configured
        if (str_contains($content, '<testsuite name="tddraft">')) {
            $this->comment('📋 PHPUnit testsuites already configured');

            return;
        }

        // Check if schema is outdated
        $isOldSchema = str_contains($content, 'schema.phpunit.de/10.') ||
                      str_contains($content, 'schema.phpunit.de/9.');

        // Show what we want to do
        $this->newLine();
        $this->comment('📋 PHPUnit configuration needs to be updated to separate TDDraft tests.');

        if ($isOldSchema) {
            $this->comment('📋 Also detected outdated PHPUnit schema - we can upgrade it too.');
            $this->comment('This will modify your phpunit.xml file to add TDDraft testsuite and update schema.');
        } else {
            $this->comment('This will modify your phpunit.xml file to add a separate testsuite for TDDraft.');
        }

        if (! $this->confirm('Do you want to automatically update phpunit.xml?', true)) {
            $this->showManualPhpUnitInstructions();

            return;
        }

        // Create backup
        $backupPath = $phpunitPath . '.tddraft-backup-' . date('Y-m-d-H-i-s');
        File::copy($phpunitPath, $backupPath);
        $this->comment("📋 Created backup: {$backupPath}");

        if ($isOldSchema && $this->confirm('Do you want to upgrade to latest PHPUnit schema (recommended)?', true)) {
            // Use complete stub with updated schema
            $stubPath = __DIR__ . '/stubs/phpunit-complete.stub';
            $newContent = File::get($stubPath);
            File::put($phpunitPath, $newContent);
            $this->info('✅ Updated phpunit.xml with new schema and TDDraft testsuite');
        } else {
            // Just update testsuites section
            $this->updateTestsuitesOnly($content, $phpunitPath);
        }
    }

    private function updateTestsuitesOnly(string $content, string $phpunitPath): void
    {
        // Find and replace testsuites section
        $pattern = '/<testsuites>.*?<\/testsuites>/s';

        $stubPath = __DIR__ . '/stubs/phpunit-testsuites.stub';
        $replacement = File::get($stubPath);

        if (preg_match($pattern, $content)) {
            $newContent = preg_replace($pattern, $replacement, $content);
            if ($newContent !== null) {
                File::put($phpunitPath, $newContent);
                $this->info('✅ Updated phpunit.xml testsuites configuration');
            } else {
                $this->warn('⚠️  Error updating phpunit.xml. Please add testsuites manually.');
                $this->showManualPhpUnitInstructions();
            }
        } else {
            $this->warn('⚠️  Could not automatically update phpunit.xml. Please add testsuites manually.');
            $this->showManualPhpUnitInstructions();
        }
    }

    private function showManualPhpUnitInstructions(): void
    {
        $this->newLine();
        $this->comment('Please manually add this to your phpunit.xml file:');
        $this->newLine();

        $stubPath = __DIR__ . '/stubs/phpunit-testsuites.stub';
        $content = File::get($stubPath);
        $this->line($content);
        $this->newLine();
    }

    private function configurePest(): void
    {
        $pestPath = base_path('tests/Pest.php');

        if (! File::exists($pestPath)) {
            $this->warn('⚠️  tests/Pest.php not found, skipping Pest configuration');

            return;
        }

        $content = File::get($pestPath);

        // Check if already configured to exclude TDDraft
        if ((str_contains($content, "->in('Feature', 'Unit')") || 
            str_contains($content, '->in("Feature", "Unit")')) ||
            (str_contains($content, "->in('Feature')") && str_contains($content, "->in('Unit')"))) {
            $this->comment('📋 Pest configuration already restricts to Feature/Unit');

            return;
        }

        $this->newLine();
        $this->comment('📋 Pest configuration needs to be updated to exclude TDDraft from main test suite.');
        $this->comment('This will modify your tests/Pest.php file to exclude TDDraft tests.');

        if (! $this->confirm('Do you want to automatically update tests/Pest.php?', true)) {
            $this->showManualPestInstructions();

            return;
        }

        // Create backup
        $backupPath = $pestPath . '.tddraft-backup-' . date('Y-m-d-H-i-s');
        File::copy($pestPath, $backupPath);
        $this->comment("📋 Created backup: {$backupPath}");

        // Look for different pest configuration patterns
        $patterns = [
            // New syntax: pest()->extend()->in()
            '/pest\(\)->extend\([^)]+\)[^;]*->in\([^)]+\);/s',
            // Old syntax: uses()->in()
            '/uses\([^)]+\)->in\(__DIR__\s*\.\s*[\'"]\/Feature[\'"],\s*__DIR__\s*\.\s*[\'"]\/Unit[\'"].*?\);/s',
            '/uses\([^)]+\)->in\([\'"]Feature[\'"],\s*[\'"]Unit[\'"].*?\);/s',
            '/uses\([^)]+\)->in\(__DIR__.*?\);/s',
            '/uses\([^)]+\);/'
        ];

        $matched = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $originalConfig = $matches[0];
                
                // Determine replacement based on the pattern found
                if (str_contains($originalConfig, 'pest()->extend')) {
                    // For new syntax, modify to include both Feature and Unit
                    $replacement = str_replace("->in('Feature')", "->in('Feature', 'Unit')", $originalConfig);
                    if ($replacement === $originalConfig) {
                        $replacement = str_replace('->in("Feature")', '->in("Feature", "Unit")', $originalConfig);
                    }
                } else {
                    // For old syntax, use the stub
                    $stubPath = __DIR__ . '/stubs/pest-exclude-tddraft.stub';
                    $newUsesContent = File::get($stubPath);
                    $replacement = $newUsesContent . "\n\n// Original configuration (commented out):\n// " . $originalConfig;
                }
                
                $newContent = str_replace($originalConfig, $replacement, $content);
                File::put($pestPath, $newContent);
                $this->info('✅ Updated tests/Pest.php to exclude TDDraft directory');
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            $this->comment('📋 No standard pest() or uses() calls found in Pest.php');
            $this->comment('📋 Please manually add restrictions to exclude TDDraft');
            $this->showManualPestInstructions();
        }
    }

    private function showManualPestInstructions(): void
    {
        $this->newLine();
        $this->comment('Please manually add this to your tests/Pest.php file:');
        $this->newLine();

        $stubPath = __DIR__ . '/stubs/pest-exclude-tddraft.stub';
        $content = File::get($stubPath);
        $this->line($content);
        $this->newLine();
    }

    private function createExampleFiles(): void
    {
        $examplePath = base_path('tests/TDDraft/ExampleDraftTest.php');

        if (File::exists($examplePath)) {
            $this->comment('📝 Example draft already exists');

            return;
        }

        $this->newLine();
        if ($this->confirm('Do you want to create an example TDDraft test to get started?', true)) {
            $stubPath = __DIR__ . '/stubs/example-draft.stub';
            $exampleContent = File::get($stubPath);

            File::put($examplePath, $exampleContent);
            $this->info('📝 Created example draft: tests/TDDraft/ExampleDraftTest.php');
        } else {
            $this->comment('📝 Skipped creating example draft test');
        }
    }
}
