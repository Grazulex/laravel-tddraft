# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.3.0] - 2026-09-17

### Added

- Laravel 13 support (`illuminate/support` and `illuminate/contracts` now accept `^12.0|^13.0`).
- Pest 4 support: `pestphp/pest` `^3.8|^4.0` and `pestphp/pest-plugin-laravel` `^3.2|^4.0`.
- CI test matrix now covers PHP 8.3 and 8.4 against Laravel 12 and 13 (Testbench 10 / 11), with `prefer-lowest` and `prefer-stable`.

### Changed

- PHP 8.3 is the minimum supported version.
- Development dependencies updated: `orchestra/testbench` `^10.0|^11.0`, Larastan 3, PHPStan 2, Rector 2, Pint 1.
- Release workflow now runs against Laravel 13 / Testbench 11.
- README badges and requirements updated for Laravel 12 / 13 and Pest 3 / 4.

### Fixed

- `tddraft:test` output callback now declares its parameter types, removing a redundant `is_string()` check flagged by PHPStan with recent `symfony/process` releases.

## [v1.2.0] - 2025-07-27

- Previous release. See the [GitHub releases](https://github.com/Grazulex/laravel-tddraft/releases) for details.

[v1.3.0]: https://github.com/Grazulex/laravel-tddraft/compare/v1.2.0...v1.3.0
[v1.2.0]: https://github.com/Grazulex/laravel-tddraft/releases/tag/v1.2.0
