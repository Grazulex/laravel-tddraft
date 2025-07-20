# Laravel TDDraft Documentation

Welcome to the Laravel TDDraft documentation. This package provides a structured approach to Test-Driven Development (TDD) in Laravel applications using Pest 3.

## What is Laravel TDDraft?

Laravel TDDraft is a Laravel package that helps you practice Test-Driven Development by providing a separate testing environment for draft tests with comprehensive status tracking. It allows you to:

- Create experimental tests without affecting your main test suite
- Practice the Red-Green-Refactor TDD cycle with automatic status monitoring
- Keep draft tests separate from production tests with unique reference tracking
- Maintain clean test suites for CI/CD pipelines with audit trails
- Access **five essential commands** for complete TDD workflow with status tracking
- **NEW: Professional status tracking** for test execution monitoring and analysis

## ⚡ Five Essential Commands

Laravel TDDraft provides **five powerful artisan commands** that work together for a complete TDD experience with comprehensive status tracking:

| Command | Purpose | Status Tracking | Documentation |
|---------|---------|----------------|---------------|
| **`tdd:init`** | Initialize TDDraft environment with status tracking setup | ✅ Sets up tracking system | [Commands](commands.md#tdd-init) |
| **`tdd:make`** | Create new draft test with unique reference tracking | ✅ Creates trackable reference | [Commands](commands.md#tdd-make) |
| **`tdd:test`** | Run draft tests with automatic status tracking | ✅ Records results & history | [Commands](commands.md#tdd-test) |
| **`tdd:list`** | List and manage draft tests with status display | ✅ Shows current & historical status | [Commands](commands.md#tdd-list) |
| **`tdd:promote`** | Graduate tests to CI suite with audit trail | ✅ Maintains tracking lineage | [Commands](commands.md#tdd-promote) |

## Quick Start

1. [Install](installation.md) the package with `composer require --dev grazulex/laravel-tddraft`
2. Run **`php artisan tdd:init`** to set up your environment with status tracking
3. Create tests with **`php artisan tdd:make "Test description"`** (gets unique reference)
4. Run **`php artisan tdd:test`** to test your drafts (automatically tracks status)
5. Use **`php artisan tdd:list --details`** to manage drafts and view status history
6. Graduate with **`php artisan tdd:promote <reference>`** when status shows consistent passing

## Documentation Sections

### Getting Started
- [Installation](installation.md) - How to install and set up Laravel TDDraft
- [Configuration](configuration.md) - Configure the package for your needs
- [Commands](commands.md) - Available artisan commands

### Usage
- [Usage Guide](usage.md) - Complete guide to using Laravel TDDraft
- [Best Practices](best-practices.md) - Recommended patterns and practices

### Advanced
- [Troubleshooting](troubleshooting.md) - Common issues and solutions
- [Contributing](../CONTRIBUTING.md) - How to contribute to the project

## Key Features

### Separate Test Environment
- Creates dedicated `tests/TDDraft/` directory
- Configures PHPUnit and Pest to exclude drafts from main test runs
- Allows independent test execution with status monitoring

### TDD Workflow Support
- Supports Red-Green-Refactor cycle with automatic status tracking
- Draft tests can start failing by design (tracked as "red" phase)
- Easy graduation of tests to main suite with promotion audit trails

### Professional Status Tracking System (NEW)
- **Comprehensive Status Monitoring**: Automatic tracking of test execution results
- **Historical Analysis**: Maintains complete status change history for each test
- **Reference-Based Tracking**: Unique identifiers for precise test lineage
- **Environment-Specific Configuration**: Customizable tracking per environment
- **Data-Driven Promotion**: Use status history to determine test readiness
- **Audit Trail Compliance**: Full traceability for enterprise requirements

### Laravel Integration
- Native Laravel package with artisan command integration
- Pest 3 compatibility with enhanced status tracking
- Professional test management and filtering capabilities

## Package Requirements

- PHP 8.3+
- Laravel 12.19+
- Pest 3.8+

## Community

- [GitHub Repository](https://github.com/Grazulex/laravel-tddraft)
- [Issues](https://github.com/Grazulex/laravel-tddraft/issues)
- [Discussions](https://github.com/Grazulex/laravel-tddraft/discussions)

## License

Laravel TDDraft is open-source software licensed under the [MIT license](../LICENSE.md).