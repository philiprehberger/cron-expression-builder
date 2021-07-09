# Changelog

All notable changes to `cron-expression-builder` will be documented in this file.

## [Unreleased]

## [1.1.0] - 2026-03-22

### Added
- `everyQuarterHour()` and `everyHalfHour()` preset methods
- `weeklyOnDay()` method accepting English day names
- `nextRunDate()` method to calculate next execution time from a cron expression

## [1.0.2] - 2026-03-17

### Changed
- Standardized package metadata, README structure, and CI workflow per package guide

## [1.0.1] - 2026-03-16

### Changed
- Standardize composer.json: add homepage, scripts
- Add Development section to README

## [1.0.0] - 2026-03-13

### Added

- `Cron` class with static shortcut methods for common schedules
- `CronBuilder` fluent builder for custom cron expressions
- `CronValidator` for validating cron expression syntax
- `CronDescriber` for human-readable cron expression descriptions
