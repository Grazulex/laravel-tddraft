# Laravel TDDraft Documentation

Welcome to the Laravel TDDraft documentation. This package provides a structured approach to Test-Driven Development (TDD) in Laravel applications using Pest 3.

## What is Laravel TDDraft?

Laravel TDDraft is a Laravel package that helps you practice Test-Driven Development by providing a separate testing environment for draft tests. It allows you to:

- Create experimental tests without affecting your main test suite
- Practice the Red-Green-Refactor TDD cycle
- Keep draft tests separate from production tests
- Maintain clean test suites for CI/CD pipelines
- Access **five essential commands** for complete TDD workflow

## ⚡ Five Essential Commands

Laravel TDDraft provides **five powerful artisan commands** that work together for a complete TDD experience:

| Command | Purpose | Documentation |
|---------|---------|---------------|
| **`tdd:init`** | Initialize TDDraft environment | [Commands](commands.md#tdd-init) |
| **`tdd:make`** | Create new draft test with tracking | [Commands](commands.md#tdd-make) |
| **`tdd:test`** | Run draft tests separately | [Commands](commands.md#tdd-test) |
| **`tdd:list`** | List and manage draft tests | [Commands](commands.md#tdd-list) |
| **`tdd:promote`** | Graduate tests to CI suite | [Commands](commands.md#tdd-promote) |

## Quick Start

1. [Install](installation.md) the package
2. Run **`php artisan tdd:init`** to set up your environment
3. Create tests with **`php artisan tdd:make "Test description"`**
4. Run **`php artisan tdd:test`** to test your drafts
5. Use **`php artisan tdd:list`** to manage drafts
6. Graduate with **`php artisan tdd:promote <reference>`**

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
- Allows independent test execution

### TDD Workflow Support
- Supports Red-Green-Refactor cycle
- Draft tests can start failing by design
- Easy graduation of tests to main suite

### Laravel Integration
- Native Laravel package
- Artisan command integration
- Pest 3 compatibility

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