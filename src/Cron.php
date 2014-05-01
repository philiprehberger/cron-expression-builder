<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder;

final class Cron
{
    public static function everyMinute(): string
    {
        return '* * * * *';
    }

    public static function everyFiveMinutes(): string
    {
        return '*/5 * * * *';
    }

    public static function everyTenMinutes(): string
    {
        return '*/10 * * * *';
    }

    public static function everyFifteenMinutes(): string
    {
        return '*/15 * * * *';
    }

    public static function everyThirtyMinutes(): string
    {
        return '*/30 * * * *';
    }

    public static function hourly(): string
    {
        return '0 * * * *';
    }

    public static function hourlyAt(int $minute): string
    {
        self::assertRange($minute, 0, 59, 'minute');

        return "{$minute} * * * *";
    }

    public static function daily(): string
    {
        return '0 0 * * *';
    }

    public static function dailyAt(string $time): string
    {
        [$hour, $minute] = self::parseTime($time);

        return "{$minute} {$hour} * * *";
    }

    public static function weekly(): string
    {
        return '0 0 * * 0';
    }

    public static function weeklyOn(int $day, string $time = '00:00'): string
    {
        self::assertRange($day, 0, 6, 'day of week');
        [$hour, $minute] = self::parseTime($time);

        return "{$minute} {$hour} * * {$day}";
    }

    public static function monthly(): string
    {
        return '0 0 1 * *';
    }

    public static function monthlyOn(int $day, string $time = '00:00'): string
    {
        self::assertRange($day, 1, 31, 'day of month');
        [$hour, $minute] = self::parseTime($time);

        return "{$minute} {$hour} {$day} * *";
    }

    public static function yearly(): string
    {
        return '0 0 1 1 *';
    }

    /**
     * Create a custom cron expression builder.
     */
    public static function custom(): CronBuilder
    {
        return new CronBuilder;
    }

    /**
     * @return array{int, int}
     */
    private static function parseTime(string $time): array
    {
        $parts = explode(':', $time);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid time format '{$time}'. Expected HH:MM.");
        }

        $hour = (int) $parts[0];
        $minute = (int) $parts[1];

        self::assertRange($hour, 0, 23, 'hour');
        self::assertRange($minute, 0, 59, 'minute');

        return [$hour, $minute];
    }

    private static function assertRange(int $value, int $min, int $max, string $field): void
    {
        if ($value < $min || $value > $max) {
            throw new \InvalidArgumentException("{$field} must be between {$min} and {$max}, got {$value}.");
        }
    }
}
