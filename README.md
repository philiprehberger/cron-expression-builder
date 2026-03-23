# PHP Cron Expression Builder

[![Tests](https://github.com/philiprehberger/cron-expression-builder/actions/workflows/tests.yml/badge.svg)](https://github.com/philiprehberger/cron-expression-builder/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/philiprehberger/cron-expression-builder.svg)](https://packagist.org/packages/philiprehberger/cron-expression-builder)
[![License](https://img.shields.io/github/license/philiprehberger/cron-expression-builder)](LICENSE)

Fluent cron expression builder with human-readable methods.

## Requirements

- PHP 8.2+

## Installation

```bash
composer require philiprehberger/cron-expression-builder
```

## Usage

### Static Shortcuts

Use the `Cron` class for common schedules:

```php
use PhilipRehberger\CronBuilder\Cron;

Cron::everyMinute();          // * * * * *
Cron::everyFiveMinutes();     // */5 * * * *
Cron::everyTenMinutes();      // */10 * * * *
Cron::everyFifteenMinutes();  // */15 * * * *
Cron::everyThirtyMinutes();   // */30 * * * *
Cron::hourly();               // 0 * * * *
Cron::hourlyAt(30);           // 30 * * * *
Cron::daily();                // 0 0 * * *
Cron::dailyAt('14:30');       // 30 14 * * *
Cron::weekly();               // 0 0 * * 0
Cron::weeklyOn(1, '09:00');   // 0 9 * * 1
Cron::monthly();              // 0 0 1 * *
Cron::monthlyOn(15, '08:00'); // 0 8 15 * *
Cron::yearly();               // 0 0 1 1 *
```

### Preset Intervals

Use builder presets for common intervals:

```php
use PhilipRehberger\CronBuilder\Cron;

Cron::custom()->everyQuarterHour()->build();                   // */15 * * * *
Cron::custom()->everyHalfHour()->build();                      // */30 * * * *
Cron::custom()->everyQuarterHour()->hour('9-17')->build();     // */15 9-17 * * *
Cron::custom()->weeklyOnDay('Monday')->minute('0')->hour('9')->build(); // 0 9 * * 1
```

### Custom Builder

Build complex expressions with the fluent builder:

```php
use PhilipRehberger\CronBuilder\Cron;

$expression = Cron::custom()
    ->minute('*/10')
    ->hour('9-17')
    ->dayOfWeek('1-5')
    ->build();

// */10 9-17 * * 1-5
```

The builder implements `Stringable`, so you can use it directly in string contexts:

```php
echo Cron::custom()->minute('0')->hour('*/2'); // 0 */2 * * *
```

### Next Run Date

Calculate the next execution time from a cron expression:

```php
use PhilipRehberger\CronBuilder\CronExpression;

$expr = new CronExpression('0 9 * * 1');
$next = $expr->nextRunDate(); // Next Monday at 09:00

// With a specific starting datetime
$from = new \DateTimeImmutable('2026-03-22 10:00:00');
$next = $expr->nextRunDate($from); // 2026-03-23 09:00
```

### Validator

Validate cron expression syntax:

```php
use PhilipRehberger\CronBuilder\CronValidator;

CronValidator::isValid('*/5 * * * *');   // true
CronValidator::isValid('60 * * * *');    // false
CronValidator::isValid('invalid');       // false
```

### Describer

Get human-readable descriptions:

```php
use PhilipRehberger\CronBuilder\CronDescriber;

CronDescriber::describe('* * * * *');      // "Every minute"
CronDescriber::describe('0 * * * *');      // "Every hour"
CronDescriber::describe('0 0 * * *');      // "Every day at midnight"
CronDescriber::describe('*/5 * * * *');    // "Every 5 minutes"
CronDescriber::describe('30 14 * * *');    // "Every day at 14:30"
CronDescriber::describe('0 0 * * 0');      // "Every Sunday at midnight"
CronDescriber::describe('0 0 1 * *');      // "First day of every month at midnight"
CronDescriber::describe('0 0 1 1 *');      // "Every year on January 1st at midnight"
```

## API

| Method | Returns | Description |
|---|---|---|
| `Cron::everyMinute()` | `string` | Every minute (`* * * * *`) |
| `Cron::everyFiveMinutes()` | `string` | Every 5 minutes (`*/5 * * * *`) |
| `Cron::everyTenMinutes()` | `string` | Every 10 minutes (`*/10 * * * *`) |
| `Cron::everyFifteenMinutes()` | `string` | Every 15 minutes (`*/15 * * * *`) |
| `Cron::everyThirtyMinutes()` | `string` | Every 30 minutes (`*/30 * * * *`) |
| `Cron::hourly()` | `string` | Every hour at :00 (`0 * * * *`) |
| `Cron::hourlyAt(int $minute)` | `string` | Every hour at given minute |
| `Cron::daily()` | `string` | Every day at midnight (`0 0 * * *`) |
| `Cron::dailyAt(string $time)` | `string` | Every day at HH:MM |
| `Cron::weekly()` | `string` | Every Sunday at midnight |
| `Cron::weeklyOn(int $day, string $time)` | `string` | Weekly on given day at time |
| `Cron::monthly()` | `string` | First of month at midnight |
| `Cron::monthlyOn(int $day, string $time)` | `string` | Monthly on given day at time |
| `Cron::yearly()` | `string` | January 1st at midnight |
| `Cron::custom()` | `CronBuilder` | Start fluent builder |
| `CronBuilder::everyQuarterHour()` | `self` | Set minute to `*/15` |
| `CronBuilder::everyHalfHour()` | `self` | Set minute to `*/30` |
| `CronBuilder::weeklyOnDay(string $dayName)` | `self` | Set day of week by English name |
| `CronExpression::nextRunDate(?DateTimeInterface $from)` | `DateTimeImmutable` | Next execution time |
| `CronValidator::isValid(string $expr)` | `bool` | Validate cron syntax |
| `CronDescriber::describe(string $expr)` | `string` | Human-readable description |

## Development

```bash
composer install
vendor/bin/phpunit
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

## License

MIT
