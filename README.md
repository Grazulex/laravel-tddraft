# Laravel TDDraft

<img src="new_logo.png" alt="Laravel TDDraft" width="200">

Test-Driven Development toolkit for Laravel applications. Automated test generation, coverage analysis, and intelligent test suggestions to accelerate your TDD workflow.

[![Latest Version](https://img.shields.io/packagist/v/grazulex/laravel-tddraft.svg?style=flat-square)](https://packagist.org/packages/grazulex/laravel-tddraft)
[![Total Downloads](https://img.shields.io/packagist/dt/grazulex/laravel-tddraft.svg?style=flat-square)](https://packagist.org/packages/grazulex/laravel-tddraft)
[![License](https://img.shields.io/github/license/grazulex/laravel-tddraft.svg?style=flat-square)](https://github.com/Grazulex/laravel-tddraft/blob/main/LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/grazulex/laravel-tddraft.svg?style=flat-square)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-12.x-ff2d20?style=flat-square&logo=laravel)](https://laravel.com/)
[![Tests](https://img.shields.io/github/actions/workflow/status/grazulex/laravel-tddraft/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Grazulex/laravel-tddraft/actions)
[![Code Style](https://img.shields.io/badge/code%20style-pint-000000?style=flat-square&logo=laravel)](https://github.com/laravel/pint)

## 📖 Table of Contents

- [Overview](#overview)
- [✨ Features](#-features)
- [📦 Installation](#-installation)
- [🚀 Quick Start](#-quick-start)
- [🧪 Test Generation](#-test-generation)
- [📊 Coverage Analysis](#-coverage-analysis)
- [🎯 Smart Suggestions](#-smart-suggestions)
- [⚙️ Configuration](#️-configuration)
- [📚 Documentation](#-documentation)
- [💡 Examples](#-examples)
- [🧪 Testing](#-testing)
- [🔧 Requirements](#-requirements)
- [🚀 Performance](#-performance)
- [🤝 Contributing](#-contributing)
- [🔒 Security](#-security)
- [📄 License](#-license)

## Overview

Laravel TDDraft is a comprehensive Test-Driven Development toolkit that accelerates your TDD workflow through automated test generation, intelligent coverage analysis, and smart test suggestions based on your code structure.

**Perfect for teams adopting TDD, maintaining high test coverage, and ensuring robust application quality.**

### 🎯 Use Cases

Laravel TDDraft is perfect for:

- **TDD Adoption** - Teams transitioning to Test-Driven Development
- **Legacy Code** - Adding tests to existing codebases
- **Quality Assurance** - Maintaining high test coverage standards  
- **API Development** - Comprehensive API testing workflows
- **Team Training** - Teaching TDD best practices

## ✨ Features

- 🚀 **Automated Test Generation** - Generate tests from existing code and specifications
- 📊 **Coverage Analysis** - Detailed code coverage reporting and visualization
- 🎯 **Smart Suggestions** - AI-powered test case recommendations
- 🔍 **Gap Detection** - Identify untested code paths and edge cases
- 📋 **Test Templates** - Customizable test templates for different scenarios
- 🎨 **IDE Integration** - Seamless integration with popular IDEs
- ✅ **Quality Metrics** - Comprehensive testing quality metrics
- 🔄 **Continuous Integration** - CI/CD pipeline integration
- 📈 **Progress Tracking** - Track testing progress and improvements
- 🧪 **Mock Generation** - Automatic mock and stub generation
- ⚡ **Performance Testing** - Built-in performance test generation
- 📝 **Documentation** - Auto-generate test documentation

## 📦 Installation

Install the package via Composer:

```bash
composer require grazulex/laravel-tddraft --dev
```

> **💡 Auto-Discovery**  
> The service provider will be automatically registered thanks to Laravel's package auto-discovery.

Publish configuration:

```bash
php artisan vendor:publish --tag=tddraft-config
```

## 🚀 Quick Start

### 1. Initialize TDDraft

```bash
php artisan tddraft:init
```

### 2. Generate Tests from Existing Code

```bash
# Generate tests for a specific class
php artisan tddraft:generate App\\Models\\User

# Generate tests for a controller
php artisan tddraft:generate App\\Http\\Controllers\\UserController

# Generate API tests
php artisan tddraft:api-tests --resource=User
```

### 3. Analyze Code Coverage

```bash
# Run coverage analysis
php artisan tddraft:coverage

# Generate coverage report
php artisan tddraft:coverage --format=html --output=coverage-report
```

### 4. Get Smart Test Suggestions

```php
use Grazulex\LaravelTddraft\Facades\TDDraft;

// Analyze class and get test suggestions
$suggestions = TDDraft::analyze(User::class);

foreach ($suggestions as $suggestion) {
    echo "Missing test: {$suggestion->getTestName()}\n";
    echo "Description: {$suggestion->getDescription()}\n";
    echo "Priority: {$suggestion->getPriority()}\n";
}

// Generate test from suggestion
$testCode = TDDraft::generateTest($suggestion);
file_put_contents('tests/Unit/UserTest.php', $testCode);
```

## 🧪 Test Generation

Laravel TDDraft provides intelligent test generation:

```bash
# Generate unit tests
php artisan tddraft:unit App\\Services\\PaymentService

# Generate feature tests  
php artisan tddraft:feature "User Registration"

# Generate integration tests
php artisan tddraft:integration App\\Http\\Controllers\\ApiController

# Generate performance tests
php artisan tddraft:performance --endpoint=/api/users --load=100
```

### Custom Test Templates

```php
// Custom test template
TDDraft::template('api_endpoint', function ($class, $method) {
    return "
    /** @test */
    public function {$method}_returns_expected_response()
    {
        \$response = \$this->postJson('/api/{$this->getEndpoint()}', [
            // Add test data
        ]);
        
        \$response->assertStatus(200)
                 ->assertJsonStructure([
                     // Add expected structure
                 ]);
    }
    ";
});
```

## 📊 Coverage Analysis

Comprehensive code coverage analysis and reporting:

```php
use Grazulex\LaravelTddraft\Coverage\Analyzer;

// Detailed coverage analysis
$analysis = Analyzer::analyze([
    'paths' => ['app/Models', 'app/Services'],
    'exclude' => ['app/Console'],
    'minimum_coverage' => 80,
]);

// Coverage metrics
echo "Overall coverage: {$analysis->getOverallCoverage()}%\n";
echo "Lines covered: {$analysis->getCoveredLines()}\n";
echo "Lines total: {$analysis->getTotalLines()}\n";

// Uncovered areas
foreach ($analysis->getUncoveredAreas() as $area) {
    echo "Uncovered: {$area->getFile()}:{$area->getLine()}\n";
    echo "Method: {$area->getMethod()}\n";
}

// Generate reports
$analysis->generateReport('html', 'coverage-reports/');
$analysis->generateReport('xml', 'coverage-reports/clover.xml');
```

## 🎯 Smart Suggestions

AI-powered test suggestions and recommendations:

```php
use Grazulex\LaravelTddraft\Intelligence\Suggester;

// Get suggestions for a class
$suggester = new Suggester();
$suggestions = $suggester->forClass(User::class);

// Different types of suggestions
foreach ($suggestions as $suggestion) {
    switch ($suggestion->getType()) {
        case 'edge_case':
            echo "Edge case: {$suggestion->getDescription()}\n";
            break;
        case 'boundary_test':
            echo "Boundary test: {$suggestion->getDescription()}\n";
            break;
        case 'error_handling':
            echo "Error handling: {$suggestion->getDescription()}\n";
            break;
    }
}

// Auto-generate suggested tests
$suggester->generateTests($suggestions, 'tests/Unit/');
```

## ⚙️ Configuration

Laravel TDDraft provides flexible configuration options:

```php
// config/tddraft.php
return [
    'test_generation' => [
        'default_namespace' => 'Tests\\Unit\\',
        'templates_path' => base_path('tests/templates'),
        'auto_format' => true,
    ],
    
    'coverage' => [
        'minimum_threshold' => 80,
        'paths' => ['app/'],
        'exclude' => ['app/Console/', 'app/Exceptions/'],
    ],
    
    'suggestions' => [
        'enabled' => true,
        'ai_powered' => true,
        'priority_levels' => ['low', 'medium', 'high', 'critical'],
    ],
];
```

## 📚 Documentation

For detailed documentation, examples, and advanced usage:

- 📚 [Full Documentation](docs/README.md)
- 🎯 [Examples](examples/README.md)
- 🔧 [Configuration](docs/configuration.md)
- 🧪 [Test Templates](docs/templates.md)
- 📊 [Coverage Reports](docs/coverage.md)

## 💡 Examples

### Automated Test Generation

```bash
# Generate comprehensive test suite for a model
php artisan tddraft:model User --with-factory --with-relationships

# Generate API test suite
php artisan tddraft:api --resource=Product --crud

# Generate tests for existing controller
php artisan tddraft:controller UserController --methods=index,store,update,destroy
```

### Coverage-Driven Development

```php
// Monitor coverage in real-time
TDDraft::coverage()->watch(['app/Services'], function ($coverage) {
    if ($coverage < 80) {
        echo "Warning: Coverage below threshold ({$coverage}%)\n";
        
        // Get suggestions to improve coverage
        $suggestions = TDDraft::suggestImprovements();
        foreach ($suggestions as $suggestion) {
            echo "Suggestion: {$suggestion->getDescription()}\n";
        }
    }
});

// Generate missing tests automatically
TDDraft::coverage()->gaps()->autoGenerate();
```

### TDD Workflow Integration

```php
// TDD workflow helper
class TDDWorkflow
{
    public function redGreenRefactor($feature)
    {
        // Red: Generate failing test
        $test = TDDraft::generateFailingTest($feature);
        file_put_contents("tests/Feature/{$feature}Test.php", $test);
        
        // Green: Implement minimum code
        $implementation = TDDraft::suggestImplementation($feature);
        
        // Refactor: Optimize and clean up
        TDDraft::refactorSuggestions($feature);
    }
}
```

Check out the [examples directory](examples) for more examples.

## 🧪 Testing

Laravel TDDraft includes comprehensive testing utilities:

```php
use Grazulex\LaravelTddraft\Testing\TddTester;

public function test_test_generation_quality()
{
    $generatedTest = TDDraft::generateTest(User::class, 'create');
    
    TddTester::make($generatedTest)
        ->assertValidSyntax()
        ->assertHasAssertions()
        ->assertFollowsConventions()
        ->assertCoversCriticalPaths();
}

public function test_coverage_accuracy()
{
    TddTester::coverage()
        ->run('tests/Unit/UserTest.php')
        ->assertCoverage('App\Models\User', 90)
        ->assertNoBranchesMissed();
}
```

## 🔧 Requirements

- PHP: ^8.3
- Laravel: ^12.0
- PHPUnit: ^11.0

## 🚀 Performance

Laravel TDDraft is optimized for performance:

- **Fast Analysis**: Optimized code parsing and analysis
- **Efficient Generation**: Intelligent template caching
- **Parallel Processing**: Multi-threaded test execution
- **Memory Efficient**: Minimal memory footprint during analysis

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 🔒 Security

If you discover a security vulnerability, please review our [Security Policy](SECURITY.md) before disclosing it.

## 📄 License

Laravel TDDraft is open-sourced software licensed under the [MIT license](LICENSE.md).

---

**Made with ❤️ for the Laravel community**

### Resources

- [📖 Documentation](docs/README.md)
- [💬 Discussions](https://github.com/Grazulex/laravel-tddraft/discussions)
- [🐛 Issue Tracker](https://github.com/Grazulex/laravel-tddraft/issues)
- [📦 Packagist](https://packagist.org/packages/grazulex/laravel-tddraft)

### Community Links

- [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md) - Our code of conduct
- [CONTRIBUTING.md](CONTRIBUTING.md) - How to contribute
- [SECURITY.md](SECURITY.md) - Security policy
- [RELEASES.md](RELEASES.md) - Release notes and changelog
