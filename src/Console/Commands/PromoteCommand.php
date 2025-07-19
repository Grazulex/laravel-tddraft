<?php

declare(strict_types=1);

namespace Grazulex\LaravelTddraft\Console\Commands;

use Exception;
use Grazulex\LaravelTddraft\Services\StatusTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class PromoteCommand extends Command
{
    protected $signature = 'tdd:promote 
                           {reference : The unique reference of the TDDraft test to promote}
                           {--target= : Target directory (Feature|Unit) - if not specified, uses the test type}
                           {--file= : Existing file to append to (optional)}
                           {--new-file= : New file name to create (optional)}
                           {--class= : Target test class name (optional)}
                           {--force : Overwrite existing files without confirmation}
                           {--keep-draft : Keep the original draft file after promotion}';

    protected $description = 'Promote a TDDraft test to the CI test suite (tests/Feature or tests/Unit)';

    public function handle(): int
    {
        $reference = $this->argument('reference');
        $targetDir = $this->option('target');
        $existingFile = $this->option('file');
        $newFile = $this->option('new-file');
        $customClass = $this->option('class');
        $force = $this->option('force');
        $keepDraft = $this->option('keep-draft');

        // Find the draft test file
        $draftFile = $this->findDraftFile($reference);
        if (! $draftFile) {
            $this->error("❌ No TDDraft test found with reference: {$reference}");

            return self::FAILURE;
        }

        $this->info("📋 Found draft test: {$draftFile}");

        // Parse the draft file to get test information
        $testInfo = $this->parseDraftFile($draftFile);
        if (! $testInfo) {
            $this->error('❌ Could not parse the draft test file');

            return self::FAILURE;
        }

        // Determine target directory
        $targetDirectory = $this->determineTargetDirectory($targetDir, $testInfo['type']);

        // Determine target file and class
        $targetPath = $this->determineTargetFile($targetDirectory, $existingFile, $newFile, $testInfo, $customClass);
        if (! $targetPath) {
            return self::FAILURE;
        }

        // Check if target file exists and handle accordingly
        if (File::exists($targetPath['file']) && ! $existingFile && ! $force && ! $this->confirm("Target file {$targetPath['file']} already exists. Overwrite?")) {
            $this->warn('🚫 Promotion cancelled');

            return self::FAILURE;
        }

        // Promote the test
        $success = $this->promoteTest($targetPath, $testInfo);

        if (! $success) {
            return self::FAILURE;
        }

        // Update status to promoted
        $statusTracker = new StatusTracker;
        $statusTracker->updateTestStatus($testInfo['reference'], 'promoted');

        // Remove draft file if not keeping it
        if (! $keepDraft) {
            File::delete($draftFile);
            $this->info("🗑️  Removed draft file: {$draftFile}");
        } else {
            $this->info('📋 Kept draft file for reference');
        }

        $this->info("✅ Successfully promoted test to: {$targetPath['file']}");
        $this->info("🎯 Test class: {$targetPath['class']}");

        return self::SUCCESS;
    }

    private function findDraftFile(string $reference): ?string
    {
        $tddraftPath = base_path('tests/TDDraft');

        if (! File::exists($tddraftPath)) {
            return null;
        }

        // Search recursively for files containing the reference
        $files = File::allFiles($tddraftPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                if (str_contains($content, $reference)) {
                    return $file->getPathname();
                }
            }
        }

        return null;
    }

    /**
     * Parse the draft file and extract metadata and test content.
     *
     * @return array{reference: string, type: string, name: string, test_content: string}|null
     */
    private function parseDraftFile(string $filePath): ?array
    {
        $content = File::get($filePath);

        // Extract test information from the file
        $info = [];

        // Extract reference
        if (preg_match('/Reference:\s*(tdd-\d{14}-[a-zA-Z0-9]{6})/', $content, $matches)) {
            $info['reference'] = $matches[1];
        }

        // Extract type
        if (preg_match('/Type:\s*(\w+)/', $content, $matches)) {
            $info['type'] = $matches[1];
        }

        // Extract test name
        if (preg_match('/TDDraft Test:\s*(.+)/', $content, $matches)) {
            $info['name'] = trim($matches[1]);
        }

        // Extract test content (everything after the comment block)
        $lines = explode("\n", $content);
        $testContentStart = false;
        $testLines = [];

        foreach ($lines as $line) {
            if (str_contains($line, "it('") || str_contains($line, 'it("')) {
                $testContentStart = true;
            }

            if ($testContentStart) {
                $testLines[] = $line;
            }
        }

        $info['test_content'] = implode("\n", $testLines);

        // Ensure all required keys are present
        $requiredKeys = ['reference', 'type', 'name', 'test_content'];
        foreach ($requiredKeys as $key) {
            if (! isset($info[$key])) {
                return null;
            }
        }

        /** @var array{reference: string, type: string, name: string, test_content: string} $info */
        return $info;
    }

    /**
     * Determine the target directory (Feature or Unit).
     */
    private function determineTargetDirectory(?string $targetDir = null, ?string $testType = null): string
    {
        if ($targetDir) {
            $dir = ucfirst(strtolower($targetDir));
            if (! in_array($dir, ['Feature', 'Unit'])) {
                $this->error("❌ Invalid target directory. Must be 'Feature' or 'Unit'");

                return '';
            }

            return $dir;
        }

        return $testType ? ucfirst($testType) : 'Feature';
    }

    /**
     * Determine the target file path and class name.
     *
     * @param  array{reference: string, type: string, name: string, test_content: string}  $testInfo
     *
     * @return array{file: string, class: string, mode?: string}|null
     */
    private function determineTargetFile(string $targetDirectory, ?string $existingFile, ?string $newFile, array $testInfo, ?string $customClass): ?array
    {
        $basePath = base_path("tests/{$targetDirectory}");

        if ($existingFile) {
            // Append to existing file
            $targetFile = $basePath . '/' . $existingFile;
            if (! str_ends_with($targetFile, '.php')) {
                $targetFile .= '.php';
            }

            if (! File::exists($targetFile)) {
                $this->error("❌ Existing file not found: {$targetFile}");

                return null;
            }

            // Extract class name from existing file
            $content = File::get($targetFile);
            if (preg_match('/class\s+(\w+)/', $content, $matches)) {
                $className = $matches[1];
            } else {
                $this->error('❌ Could not determine class name from existing file');

                return null;
            }

            return [
                'file' => $targetFile,
                'class' => $className,
                'mode' => 'append',
            ];
        }

        if ($newFile) {
            // Create new file with specified name
            $fileName = $newFile;
            if (! str_ends_with($fileName, '.php')) {
                $fileName .= '.php';
            }

            $className = $customClass ?: str_replace('.php', '', $fileName);
        } else {
            // Generate file name from test name
            $fileName = Str::studly($testInfo['name']) . 'Test.php';
            $className = $customClass ?: str_replace('.php', '', $fileName);
        }

        return [
            'file' => $basePath . '/' . $fileName,
            'class' => $className,
            'mode' => 'create',
        ];
    }

    /**
     * Promote the test to the target location.
     *
     * @param  array{file: string, class: string, mode?: string}  $targetPath
     * @param  array{reference: string, type: string, name: string, test_content: string}  $testInfo
     */
    private function promoteTest(array $targetPath, array $testInfo): bool
    {
        try {
            if (isset($targetPath['mode']) && $targetPath['mode'] === 'append') {
                return $this->appendToExistingFile($targetPath['file'], $testInfo);
            }

            return $this->createNewFile($targetPath, $testInfo);
        } catch (Exception $e) {
            $this->error("❌ Error promoting test: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Append test content to an existing file.
     *
     * @param  array{reference: string, type: string, name: string, test_content: string}  $testInfo
     */
    private function appendToExistingFile(string $targetFile, array $testInfo): bool
    {
        $content = File::get($targetFile);

        // Find the last closing brace and insert before it
        $lastBracePos = strrpos($content, '}');
        if ($lastBracePos === false) {
            $this->error('❌ Could not find where to insert test in existing file');

            return false;
        }

        // Clean up the test content (remove draft-specific comments)
        $cleanTestContent = $this->cleanTestContent($testInfo['test_content']);

        // Insert the test
        $beforeBrace = substr($content, 0, $lastBracePos);
        $afterBrace = substr($content, $lastBracePos);

        $newContent = $beforeBrace . "\n" . $cleanTestContent . "\n" . $afterBrace;

        File::put($targetFile, $newContent);

        return true;
    }

    /**
     * Create a new test file.
     *
     * @param  array{file: string, class: string, mode?: string}  $targetPath
     * @param  array{reference: string, type: string, name: string, test_content: string}  $testInfo
     */
    private function createNewFile(array $targetPath, array $testInfo): bool
    {
        // Ensure target directory exists
        $targetDir = dirname($targetPath['file']);
        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        // Generate new test file content
        $stubPath = __DIR__ . '/stubs/promoted-test.stub';

        if (! File::exists($stubPath)) {
            // Create content without stub
            $content = $this->generatePromotedTestContent($targetPath['class'], $testInfo);
        } else {
            $content = File::get($stubPath);
            $content = str_replace('{{className}}', $targetPath['class'], $content);
            $content = str_replace('{{testContent}}', $this->cleanTestContent($testInfo['test_content']), $content);
            $content = str_replace('{{originalReference}}', $testInfo['reference'], $content);
            $content = str_replace('{{originalName}}', $testInfo['name'], $content);
            $content = str_replace('{{promotedDate}}', now()->format('Y-m-d H:i:s'), $content);
        }

        File::put($targetPath['file'], $content);

        return true;
    }

    private function cleanTestContent(string $testContent): string
    {
        // Remove TDDraft-specific comments and cleanup
        $lines = explode("\n", $testContent);
        $cleanedLines = [];

        foreach ($lines as $line) {
            // Skip TDDraft-specific comments
            if (str_contains($line, 'TODO: Implement your test')) {
                continue;
            }
            if (str_contains($line, 'This test starts in the')) {
                continue;
            }
            if (str_contains($line, '1. Write the test')) {
                continue;
            }
            if (str_contains($line, '2. Make it pass')) {
                continue;
            }
            if (str_contains($line, '3. Promote it to')) {
                continue;
            }
            if (str_contains($line, "expect(true)->toBeTrue('Replace this")) {
                continue;
            }
            $cleanedLines[] = $line;
        }

        return implode("\n", $cleanedLines);
    }

    /**
     * Generate the content for the promoted test file.
     *
     * @param  array{reference: string, type: string, name: string, test_content: string}  $testInfo
     */
    private function generatePromotedTestContent(string $className, array $testInfo): string
    {
        $cleanTestContent = $this->cleanTestContent($testInfo['test_content']);

        return "<?php

declare(strict_types=1);

/**
 * Promoted from TDDraft
 * Original Reference: {$testInfo['reference']}
 * Original Name: {$testInfo['name']}
 */

class {$className} extends TestCase
{
{$cleanTestContent}
}
";
    }
}
