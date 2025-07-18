# Laravel TDDraft

<div align="center">
  <img src="new_logo.png" alt="Laravel TDDraft" width="100">
  <p><strong>Write, track, and promote exploratory TDD scenarios in Laravel using Pest 3 — without polluting your CI.</strong></p>

  [![Latest Version](https://img.shields.io/packagist/v/grazulex/laravel-tddraft)](https://packagist.org/packages/grazulex/laravel-tddraft)
  [![Total Downloads](https://img.shields.io/packagist/dt/grazulex/laravel-tddraft)](https://packagist.org/packages/grazulex/laravel-tddraft)
  [![License](https://img.shields.io/github/license/grazulex/laravel-tddraft)](LICENSE.md)
  [![PHP Version](https://img.shields.io/badge/php-%5E8.3-blue)](https://php.net)
  [![Laravel Version](https://img.shields.io/badge/laravel-%5E12.19-red)](https://laravel.com)
  [![Pest Version](https://img.shields.io/badge/pest-%5E3.8-purple)](https://pestphp.com)
  [![Code Style](https://img.shields.io/badge/code%20style-pint-orange)](https://github.com/laravel/pint)
</div>

## Overview

<div style="background: linear-gradient(135deg, #FF9900 0%, #D2D200 25%, #88C600 75%, #00B470 100%); padding: 20px; border-radius: 10px; margin: 20px 0; color: #ffffff; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">

**Laravel TDDraft** lets you write temporary TDD-focused test drafts — exploratory specs that start red by design and live outside your main test suite.  
Drafts won't fail your CI, but they help you track your development progress and promote green specs into full tests when ready.

</div>

## ✨ Features

- 📂 Dedicated `tests/TDDraft/` directory
- 🚦 Tracks which specs are still failing vs passing
- ✅ Promote passing drafts to full tests via command
- 🧪 Native Pest 3 support (`#[Group('tddraft')]`, `--profile`)
- 🌱 Scenario-based seed support per draft
- 🧹 CLI tooling to list, test, prune and manage drafts
- 🎯 Built for clean, incremental TDD

## 🚀 Quick Start

```bash
composer require --dev grazulex/laravel-tddraft
php artisan vendor:publish --tag=tddraft-config
```

> 💡 Laravel TDDraft requires Pest v3. Make sure you have it installed:

```bash
composer require pestphp/pest --dev
php artisan pest:install
```

## 🛠 Draft Workflow

### 1. Create a draft

```bash
php artisan tdd:make "User can subscribe"
```

Creates:

```bash
tests/TDDraft/UserCanSubscribeTest.php
```

### 2. Write your exploratory spec

```php
#[Group('tddraft')]
it('lets a user subscribe with email')->todo();
```

Or use fluent syntax:

```php
draft('guest subscribes to newsletter')
    ->givenSeed('users:guest')
    ->when(fn () => post('/newsletter', ['email' => 'a@b.com']))
    ->then(fn ($response) => $response->assertStatus(200));
```

### 3. Run your TDD specs

```bash
php artisan tdd:test
# or
pest --profile=tddraft
```

### 4. Promote a green draft

```bash
php artisan tdd:promote tests/TDDraft/UserCanSubscribeTest.php
```

Moves test to `Feature/` and removes `todo()`/tag.

## 📦 Commands

| Command | Description |
|--------|-------------|
| `tdd:make` | Create a new draft test |
| `tdd:test` | Run only the draft specs |
| `tdd:list` | List all drafts and their statuses |
| `tdd:promote` | Move draft → full test |
| `tdd:prune` | Delete green drafts |
| `tdd:seed` | Run TDD-specific seeders |

## 📁 Config

```php
return [
    'draft_path' => base_path('tests/TDDraft'),
    'seeder_path' => base_path('database/tddraft_seeders'),
];
```

## 🧪 Example Test

```php
#[Group('tddraft')]
it('fails until implemented', function () {
    expect(true)->toBeFalse(); // red by design
});
```

## ✅ When ready

Promote it:

```bash
php artisan tdd:promote tests/TDDraft/MyTest.php
```

And use it as a real test under `tests/Feature/`.

---

<div align="center">
  Made with <span style="color: #FF9900;">❤️</span> for the <span style="color: #88C600;">Laravel</span> and <span style="color: #D2D200;">Pest</span> community
</div>