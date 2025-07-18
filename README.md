# Laravel LaravelTddraft

![Tests](https://github.com/grazulex/laravel-tddraft/workflows/tests/badge.svg)
![Code Style](https://github.com/grazulex/laravel-tddraft/workflows/code-style/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/grazulex/laravel-tddraft/v/stable)](https://packagist.org/packages/grazulex/laravel-tddraft)
[![Total Downloads](https://poser.pugx.org/grazulex/laravel-tddraft/downloads)](https://packagist.org/packages/grazulex/laravel-tddraft)
[![License](https://poser.pugx.org/grazulex/laravel-tddraft/license)](https://packagist.org/packages/grazulex/laravel-tddraft)

A powerful Laravel package that provides LaravelTddraft functionality with modern PHP 8.3+ features.

## Features

- ✅ Modern PHP 8.3+ syntax
- ✅ Laravel 12+ compatibility
- ✅ Full test coverage with Pest
- ✅ Code quality tools (PHPStan, Pint, Rector)
- ✅ Auto-discoverable service provider
- ✅ Configurable and extensible
- ✅ Comprehensive documentation

## Installation

You can install the package via Composer:

```bash
composer require grazulex/laravel-tddraft
```

The package will automatically register its service provider.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="tddraft-config"
```

This will create a `config/tddraft.php` file where you can customize the package behavior.

## Usage

### Basic Usage

```php
// Basic usage example
use \SomeClass;

$instance = new SomeClass();
$result = $instance->doSomething();
```

### Advanced Usage

```php
// Advanced usage with configuration
$config = config('tddraft');
// Your advanced implementation here
```

## Testing

Run the tests with:

```bash
composer test
```

## Code Quality

This package uses several tools to ensure code quality:

```bash
composer run pint      # Code formatting
composer run phpstan   # Static analysis
composer run rector    # Code refactoring
composer run full      # Run all quality checks + tests
```

## Changelog

Please see [RELEASES](RELEASES.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jean-Marc Strauven](https://github.com/grazulex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
