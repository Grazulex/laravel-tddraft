<?php

declare(strict_types=1);

/**
 * Status Tracking Analysis Example for Laravel TDDraft
 *
 * This example demonstrates advanced status tracking analysis patterns
 * and how to use status data for making informed test promotion decisions.
 */
echo "Laravel TDDraft - Status Tracking Analysis Example\n";
echo "=================================================\n\n";

echo "This example shows how to analyze test status data for professional\n";
echo "TDD workflow management and data-driven promotion decisions.\n\n";

// Example 1: Understanding Status Tracking Data Structure
echo "1. Understanding Status Tracking Data Structure\n";
echo "-----------------------------------------------\n";
echo "Laravel TDDraft automatically tracks test execution in tests/TDDraft/.status.json:\n\n";

$statusExample = <<<'JSON'
{
  "tdd-20250718142530-Abc123": {
    "status": "passed",
    "updated_at": "2025-07-18T14:30:45+00:00",
    "history": [
      {
        "status": "failed",
        "timestamp": "2025-07-18T14:25:30+00:00"
      },
      {
        "status": "failed", 
        "timestamp": "2025-07-18T14:27:15+00:00"
      }
    ]
  },
  "tdd-20250718141045-Def456": {
    "status": "failed",
    "updated_at": "2025-07-18T14:32:10+00:00",
    "history": [
      {
        "status": "passed",
        "timestamp": "2025-07-18T14:30:00+00:00"
      }
    ]
  },
  "tdd-20250718153010-Ghi789": {
    "status": "passed",
    "updated_at": "2025-07-18T15:30:10+00:00",
    "history": []
  }
}
JSON;

echo "Example status file structure:\n";
echo $statusExample . "\n\n";

echo "Key insights from this data:\n";
echo "• Abc123: Red→Red→Green pattern (typical TDD flow, now stable)\n";
echo "• Def456: Green→Red pattern (regression, needs attention)\n";
echo "• Ghi789: No history, consistently passing (promotion candidate)\n\n";

// Example 2: Status Analysis Scripts
echo "2. Status Analysis Scripts\n";
echo "--------------------------\n";
echo "Create custom scripts to analyze test stability and readiness:\n\n";

$analysisScript = <<<'PHP'
#!/usr/bin/env php
<?php
/**
 * Analyze TDDraft test stability using status tracking data
 * Usage: php scripts/analyze-test-stability.php
 */

$statusFile = base_path('tests/TDDraft/.status.json');

if (!file_exists($statusFile)) {
    echo "❌ No status tracking data found. Run 'php artisan tdd:test' first.\n";
    exit(1);
}

$statuses = json_decode(file_get_contents($statusFile), true);

if (empty($statuses)) {
    echo "📊 No test data available\n";
    exit(0);
}

echo "📈 TDDraft Test Stability Analysis\n";
echo "==================================\n\n";

$stable = [];
$unstable = [];
$failing = [];
$regressions = [];

foreach ($statuses as $reference => $data) {
    $currentStatus = $data['status'];
    $historyCount = count($data['history'] ?? []);
    
    if ($currentStatus === 'passed' && $historyCount === 0) {
        $stable[] = $reference;
    } elseif ($historyCount > 3) {
        $unstable[] = [$reference, $historyCount];
    } elseif ($currentStatus === 'failed') {
        // Check if this is a regression (was passing, now failing)
        $lastStatus = end($data['history'])['status'] ?? null;
        if ($lastStatus === 'passed') {
            $regressions[] = $reference;
        } else {
            $failing[] = $reference;
        }
    }
}

// Display results
echo "✅ Stable Tests (ready for promotion): " . count($stable) . "\n";
foreach ($stable as $ref) {
    echo "   • $ref - Consistently passing, no status changes\n";
}

echo "\n🚀 Promotion Recommendations:\n";
foreach ($stable as $ref) {
    echo "   php artisan tdd:promote $ref\n";
}

echo "\n⚠️  Unstable Tests (frequent status changes): " . count($unstable) . "\n";
foreach ($unstable as [$ref, $changeCount]) {
    echo "   • $ref - $changeCount status changes (needs stabilization)\n";
}

echo "\n🔄 Regression Tests (passed → failed): " . count($regressions) . "\n";
foreach ($regressions as $ref) {
    echo "   • $ref - Recently broke, priority for fixing\n";
}

echo "\n❌ Consistently Failing Tests: " . count($failing) . "\n";
foreach ($failing as $ref) {
    echo "   • $ref - Needs implementation or fixing\n";
}

// Performance metrics
$totalTests = count($statuses);
$passingTests = count(array_filter($statuses, fn($data) => $data['status'] === 'passed'));
$passingRate = $totalTests > 0 ? round(($passingTests / $totalTests) * 100, 1) : 0;

echo "\n📊 Summary Statistics:\n";
echo "   Total Tests: $totalTests\n";
echo "   Passing: $passingTests ($passingRate%)\n";
echo "   Stable (promotion ready): " . count($stable) . "\n";
echo "   Unstable: " . count($unstable) . "\n";
echo "   Regressions: " . count($regressions) . "\n";
PHP;

echo "scripts/analyze-test-stability.php:\n";
echo $analysisScript . "\n\n";

// Example 3: Automated Promotion Workflow
echo "3. Automated Promotion Workflow\n";
echo "-------------------------------\n";
echo "Use status data to automate test promotion decisions:\n\n";

$promotionScript = <<<'BASH'
#!/bin/bash
# scripts/auto-promote-stable-tests.sh

echo "🔍 Finding stable tests for promotion..."

STATUS_FILE="tests/TDDraft/.status.json"

if [ ! -f "$STATUS_FILE" ]; then
    echo "❌ Status file not found. Run 'php artisan tdd:test' first."
    exit 1
fi

# Find stable tests (passed status with no history)
STABLE_TESTS=$(jq -r '
    to_entries[] | 
    select(.value.status == "passed" and (.value.history | length) == 0) | 
    .key
' "$STATUS_FILE")

if [ -z "$STABLE_TESTS" ]; then
    echo "📊 No stable tests found ready for promotion."
    exit 0
fi

echo "✅ Found stable tests ready for promotion:"
echo "$STABLE_TESTS"
echo

# Confirm before promotion
read -p "Promote all stable tests? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    for TEST_REF in $STABLE_TESTS; do
        echo "🚀 Promoting $TEST_REF..."
        php artisan tdd:promote "$TEST_REF"
        
        if [ $? -eq 0 ]; then
            echo "✅ Successfully promoted $TEST_REF"
        else
            echo "❌ Failed to promote $TEST_REF"
        fi
    done
    
    echo "🎉 Automated promotion complete!"
    echo "💡 Run 'pest' to verify promoted tests work in CI suite"
else
    echo "❌ Promotion cancelled"
fi
BASH;

echo $promotionScript . "\n\n";

// Example 4: CI Integration
echo "4. CI Integration Patterns\n";
echo "-------------------------\n";
echo "Integrate status tracking into your CI/CD pipeline:\n\n";

$ciIntegration = <<<'YAML'
# .github/workflows/tddraft-quality-check.yml
name: TDDraft Quality Check

on:
  push:
    branches: [ develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  tddraft-analysis:
    runs-on: ubuntu-latest
    if: contains(github.event.head_commit.message, '[tddraft]')
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
    
    - name: Install dependencies
      run: composer install --no-interaction --prefer-dist
    
    - name: Run TDDraft tests with status tracking
      run: |
        php artisan tdd:init --no-interaction
        php artisan tdd:test
    
    - name: Analyze test stability
      run: |
        if [ -f "tests/TDDraft/.status.json" ]; then
          echo "📊 TDDraft Test Status Analysis"
          echo "==============================="
          
          # Count test statuses
          total=$(jq '. | length' tests/TDDraft/.status.json)
          passed=$(jq '[.[] | select(.status == "passed")] | length' tests/TDDraft/.status.json)
          failed=$(jq '[.[] | select(.status == "failed")] | length' tests/TDDraft/.status.json)
          
          # Find stable tests (candidates for promotion)
          stable=$(jq '[.[] | select(.status == "passed" and (.history | length == 0))] | length' tests/TDDraft/.status.json)
          
          # Find unstable tests (frequent changes)
          unstable=$(jq '[.[] | select(.history | length > 3)] | length' tests/TDDraft/.status.json)
          
          echo "📈 Statistics:"
          echo "   Total Tests: $total"
          echo "   Passed: $passed"
          echo "   Failed: $failed"  
          echo "   Stable (promotion ready): $stable"
          echo "   Unstable (needs attention): $unstable"
          
          # Set GitHub Actions outputs
          echo "total_tests=$total" >> $GITHUB_OUTPUT
          echo "stable_tests=$stable" >> $GITHUB_OUTPUT
          echo "unstable_tests=$unstable" >> $GITHUB_OUTPUT
          
          # Fail if too many unstable tests
          if [ "$unstable" -gt 2 ]; then
            echo "⚠️  Too many unstable tests ($unstable). Consider stabilizing before merge."
            exit 1
          fi
        else
          echo "ℹ️  No TDDraft status data found"
        fi
    
    - name: Comment PR with analysis
      if: github.event_name == 'pull_request'
      uses: actions/github-script@v6
      with:
        script: |
          const fs = require('fs');
          
          if (fs.existsSync('tests/TDDraft/.status.json')) {
            const status = JSON.parse(fs.readFileSync('tests/TDDraft/.status.json', 'utf8'));
            const total = Object.keys(status).length;
            const passed = Object.values(status).filter(s => s.status === 'passed').length;
            const stable = Object.values(status).filter(s => s.status === 'passed' && s.history.length === 0).length;
            
            const comment = `## 📊 TDDraft Test Analysis
            
            - **Total Tests**: ${total}
            - **Passing**: ${passed}
            - **Stable (ready for promotion)**: ${stable}
            
            ${stable > 0 ? `### 🚀 Promotion Candidates\nThe following tests appear stable and may be ready for promotion:\n` + Object.entries(status).filter(([ref, data]) => data.status === 'passed' && data.history.length === 0).map(([ref]) => `- \`${ref}\``).join('\n') : ''}
            `;
            
            github.rest.issues.createComment({
              issue_number: context.issue.number,
              owner: context.repo.owner,
              repo: context.repo.repo,
              body: comment
            });
          }
YAML;

echo "GitHub Actions workflow:\n";
echo $ciIntegration . "\n\n";

// Example 5: Custom Status Analysis Functions
echo "5. Custom Status Analysis Functions\n";
echo "-----------------------------------\n";
echo "Build custom PHP functions for advanced status analysis:\n\n";

$customAnalysis = <<<'PHP'
<?php
/**
 * Custom Laravel TDDraft Status Analysis Functions
 */

class TDDraftStatusAnalyzer 
{
    private array $statusData;
    
    public function __construct(string $statusFilePath = 'tests/TDDraft/.status.json')
    {
        $fullPath = base_path($statusFilePath);
        $this->statusData = file_exists($fullPath) 
            ? json_decode(file_get_contents($fullPath), true) 
            : [];
    }
    
    /**
     * Get tests that are consistently passing (promotion candidates)
     */
    public function getStableTests(): array
    {
        return array_filter($this->statusData, function($data, $ref) {
            return $data['status'] === 'passed' && empty($data['history']);
        }, ARRAY_FILTER_USE_BOTH);
    }
    
    /**
     * Get tests with frequent status changes (unstable)
     */
    public function getUnstableTests(int $changeThreshold = 3): array
    {
        return array_filter($this->statusData, function($data) use ($changeThreshold) {
            return count($data['history'] ?? []) > $changeThreshold;
        });
    }
    
    /**
     * Get tests that recently regressed (passed → failed)
     */
    public function getRegressionTests(): array
    {
        return array_filter($this->statusData, function($data) {
            if ($data['status'] !== 'failed') return false;
            
            $lastHistoryStatus = end($data['history'])['status'] ?? null;
            return $lastHistoryStatus === 'passed';
        });
    }
    
    /**
     * Calculate test stability metrics
     */
    public function getStabilityMetrics(): array
    {
        $total = count($this->statusData);
        if ($total === 0) return ['total' => 0];
        
        $passing = count(array_filter($this->statusData, fn($d) => $d['status'] === 'passed'));
        $stable = count($this->getStableTests());
        $unstable = count($this->getUnstableTests());
        $regressions = count($this->getRegressionTests());
        
        return [
            'total' => $total,
            'passing' => $passing,
            'passing_rate' => round(($passing / $total) * 100, 1),
            'stable' => $stable,
            'unstable' => $unstable,
            'regressions' => $regressions,
            'stability_score' => round(($stable / $total) * 100, 1),
        ];
    }
    
    /**
     * Generate promotion recommendations
     */
    public function getPromotionRecommendations(): array
    {
        $stable = $this->getStableTests();
        $recommendations = [];
        
        foreach ($stable as $ref => $data) {
            $recommendations[] = [
                'reference' => $ref,
                'command' => "php artisan tdd:promote {$ref}",
                'last_run' => $data['updated_at'],
                'confidence' => 'high', // No history means consistently passing
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Identify tests needing attention
     */
    public function getAttentionNeeded(): array
    {
        $attention = [];
        
        // Add regressions (high priority)
        foreach ($this->getRegressionTests() as $ref => $data) {
            $attention[$ref] = [
                'priority' => 'high',
                'reason' => 'Recent regression (passed → failed)',
                'last_change' => $data['updated_at'],
            ];
        }
        
        // Add unstable tests (medium priority)
        foreach ($this->getUnstableTests() as $ref => $data) {
            if (!isset($attention[$ref])) {
                $changeCount = count($data['history']);
                $attention[$ref] = [
                    'priority' => 'medium',
                    'reason' => "Unstable ($changeCount status changes)",
                    'last_change' => $data['updated_at'],
                ];
            }
        }
        
        return $attention;
    }
}

// Usage example:
$analyzer = new TDDraftStatusAnalyzer();

echo "📊 Test Stability Metrics:\n";
$metrics = $analyzer->getStabilityMetrics();
foreach ($metrics as $key => $value) {
    echo "   " . ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
}

echo "\n🚀 Promotion Recommendations:\n";
foreach ($analyzer->getPromotionRecommendations() as $rec) {
    echo "   {$rec['reference']} - {$rec['command']}\n";
}

echo "\n⚠️  Tests Needing Attention:\n";
foreach ($analyzer->getAttentionNeeded() as $ref => $info) {
    echo "   $ref - {$info['priority']} priority: {$info['reason']}\n";
}
PHP;

echo $customAnalysis . "\n\n";

// Example 6: Integration with Laravel Commands
echo "6. Integration with Laravel Commands\n";
echo "-----------------------------------\n";
echo "Create custom Artisan commands for status analysis:\n\n";

$customCommand = <<<'PHP'
<?php
/**
 * Custom Artisan command for TDDraft status analysis
 * 
 * Create in: app/Console/Commands/TddraftAnalyzeCommand.php
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TddraftAnalyzeCommand extends Command
{
    protected $signature = 'tddraft:analyze 
                           {--promote : Automatically promote stable tests}
                           {--report : Generate detailed report}';
    
    protected $description = 'Analyze TDDraft test status and stability';
    
    public function handle(): int
    {
        $statusFile = base_path('tests/TDDraft/.status.json');
        
        if (!file_exists($statusFile)) {
            $this->error('❌ No status data found. Run "php artisan tdd:test" first.');
            return self::FAILURE;
        }
        
        $analyzer = new TDDraftStatusAnalyzer();
        $metrics = $analyzer->getStabilityMetrics();
        
        $this->info('📊 TDDraft Status Analysis');
        $this->line('===========================');
        
        // Display metrics
        $this->table(['Metric', 'Value'], [
            ['Total Tests', $metrics['total']],
            ['Passing Tests', $metrics['passing']],
            ['Passing Rate', $metrics['passing_rate'] . '%'],
            ['Stable Tests', $metrics['stable']],
            ['Unstable Tests', $metrics['unstable']],
            ['Regressions', $metrics['regressions']],
            ['Stability Score', $metrics['stability_score'] . '%'],
        ]);
        
        // Show promotion candidates
        $promotionRecs = $analyzer->getPromotionRecommendations();
        if (!empty($promotionRecs)) {
            $this->newLine();
            $this->info('🚀 Promotion Candidates (' . count($promotionRecs) . ')');
            foreach ($promotionRecs as $rec) {
                $this->line("   • {$rec['reference']}");
            }
            
            if ($this->option('promote')) {
                if ($this->confirm('Promote all stable tests?')) {
                    foreach ($promotionRecs as $rec) {
                        $this->call('tdd:promote', ['reference' => $rec['reference']]);
                    }
                }
            }
        }
        
        // Show tests needing attention
        $attention = $analyzer->getAttentionNeeded();
        if (!empty($attention)) {
            $this->newLine();
            $this->warn('⚠️  Tests Needing Attention (' . count($attention) . ')');
            foreach ($attention as $ref => $info) {
                $priority = $info['priority'] === 'high' ? '<fg=red>HIGH</>' : '<fg=yellow>MEDIUM</>';
                $this->line("   • $ref - $priority: {$info['reason']}");
            }
        }
        
        if ($this->option('report')) {
            $this->generateDetailedReport($analyzer);
        }
        
        return self::SUCCESS;
    }
    
    private function generateDetailedReport(TDDraftStatusAnalyzer $analyzer): void
    {
        $reportPath = storage_path('logs/tddraft-analysis-' . date('Y-m-d-H-i-s') . '.json');
        
        $report = [
            'timestamp' => now()->toISOString(),
            'metrics' => $analyzer->getStabilityMetrics(),
            'promotion_candidates' => $analyzer->getPromotionRecommendations(),
            'attention_needed' => $analyzer->getAttentionNeeded(),
            'stable_tests' => array_keys($analyzer->getStableTests()),
            'unstable_tests' => array_keys($analyzer->getUnstableTests()),
            'regression_tests' => array_keys($analyzer->getRegressionTests()),
        ];
        
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
        $this->info("📄 Detailed report saved to: $reportPath");
    }
}
PHP;

echo "Custom Artisan command:\n";
echo $customCommand . "\n\n";

echo "✅ Status Tracking Analysis Complete!\n";
echo "\nKey Patterns Demonstrated:\n";
echo "• **Data-driven test management** using status history\n";
echo "• **Automated promotion workflows** based on stability\n";
echo "• **CI/CD integration** with status analysis\n";
echo "• **Custom analysis tools** for professional workflows\n";
echo "• **Regression detection** and priority management\n";
echo "• **Stability scoring** for quality metrics\n\n";
echo "These patterns enable professional TDD workflows with full\n";
echo "traceability and data-driven decision making.\n\n";
echo "For basic usage, see examples/basic-usage.php\n";
echo "For advanced patterns, see examples/advanced-usage.php\n";
echo "For complete documentation, see docs/\n";
