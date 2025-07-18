<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakeCommand extends Command
{
    protected $signature = 'tdd:make 
                           {name : The name of the test}
                           {--type=feature : Test type (feature|unit)}
                           {--path= : Custom path within tests/TDDraft (optional)}
                           {--class= : Custom class name (optional)}';

    protected $description = 'Create a new TDDraft test with unique reference tracking';

    public function handle(): int
    {
        $name = $this->argument('name');
        $typeOption = $this->option('type');
        $type = strtolower($typeOption ?? 'feature');
        $customPath = $this->option('path');
        $customClass = $this->option('class');

        // Validate type
        if (! in_array($type, ['feature', 'unit'])) {
            $this->error('❌ Test type must be either "feature" or "unit"');

            return self::FAILURE;
        }

        // Generate unique reference
        $uniqueRef = $this->generateUniqueReference();

        // Determine file path and class name
        $filePath = $this->determineFilePath($name, $customPath);
        $this->determineClassName($name, $customClass);

        // Check if file already exists
        if (File::exists($filePath)) {
            $this->error("❌ Test file already exists: {$filePath}");

            return self::FAILURE;
        }

        // Create directory if needed
        $directory = dirname($filePath);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
            $this->info("📂 Created directory: {$directory}");
        }

        // Generate test content
        $content = $this->generateTestContent($name, $type, $uniqueRef);

        // Write file
        File::put($filePath, $content);

        $this->newLine();
        $this->info('✅ TDDraft test created successfully!');
        $this->line("📄 File: {$filePath}");
        $this->line("🔖 Reference: {$uniqueRef}");
        $this->line("🏷️  Type: {$type}");
        $this->newLine();
        $this->comment('Next steps:');
        $this->line("  • Run your draft test: php artisan tdd:test --filter=\"{$name}\"");
        $this->line('  • Edit the test to implement your scenario');
        $this->line("  • When ready, promote to main suite: mv {$filePath} tests/" . ucfirst($type) . '/');

        return self::SUCCESS;
    }

    private function generateUniqueReference(): string
    {
        // Generate a unique reference with timestamp and random component
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);

        return "tdd-{$timestamp}-{$random}";
    }

    private function determineFilePath(string $name, ?string $customPath): string
    {
        $basePath = base_path('tests/TDDraft');

        // If custom path is provided, use it
        if ($customPath) {
            $customPath = trim($customPath, '/');
            $basePath .= '/' . $customPath;
        }

        // Generate filename from name
        $filename = $this->generateFilename($name);

        return $basePath . '/' . $filename;
    }

    private function generateFilename(string $name): string
    {
        // Convert name to StudlyCase and add Test suffix
        $filename = Str::studly($name);

        // Ensure it ends with Test
        if (! Str::endsWith($filename, 'Test')) {
            $filename .= 'Test';
        }

        return $filename . '.php';
    }

    private function determineClassName(string $name, ?string $customClass): string
    {
        if ($customClass) {
            return $customClass;
        }

        $className = Str::studly($name);

        // Ensure it ends with Test
        if (! Str::endsWith($className, 'Test')) {
            $className .= 'Test';
        }

        return $className;
    }

    private function generateTestContent(string $name, string $type, string $uniqueRef): string
    {
        $description = Str::lower($name);
        $testName = Str::slug($name, ' ');
        $typeUcfirst = ucfirst($type);
        $created = now()->format('Y-m-d H:i:s');

        // Load template from stub
        $stubPath = __DIR__ . '/stubs/test-template.stub';
        $template = File::get($stubPath);

        // Replace template variables
        $content = str_replace([
            '{{name}}',
            '{{uniqueRef}}',
            '{{type}}',
            '{{typeUcfirst}}',
            '{{created}}',
            '{{testName}}',
            '{{description}}',
        ], [
            $name,
            $uniqueRef,
            $type,
            $typeUcfirst,
            $created,
            $testName,
            $description,
        ], $template);

        return "<?php\n\ndeclare(strict_types=1);\n\n" . $content;
    }
}
