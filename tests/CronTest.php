<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder\Tests;

use InvalidArgumentException;
use PhilipRehberger\CronBuilder\Cron;
use PhilipRehberger\CronBuilder\CronDescriber;
use PhilipRehberger\CronBuilder\CronExpression;
use PhilipRehberger\CronBuilder\CronValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CronTest extends TestCase
{
    #[Test]
    public function test_every_minute(): void
    {
        $this->assertSame('* * * * *', Cron::everyMinute());
    }

    #[Test]
    public function test_every_five_minutes(): void
    {
        $this->assertSame('*/5 * * * *', Cron::everyFiveMinutes());
    }

    #[Test]
    public function test_every_fifteen_minutes(): void
    {
        $this->assertSame('*/15 * * * *', Cron::everyFifteenMinutes());
    }

    #[Test]
    public function test_hourly(): void
    {
        $this->assertSame('0 * * * *', Cron::hourly());
    }

    #[Test]
    public function test_hourly_at(): void
    {
        $this->assertSame('30 * * * *', Cron::hourlyAt(30));
    }

    #[Test]
    public function test_daily(): void
    {
        $this->assertSame('0 0 * * *', Cron::daily());
    }

    #[Test]
    public function test_daily_at(): void
    {
        $this->assertSame('30 14 * * *', Cron::dailyAt('14:30'));
    }

    #[Test]
    public function test_weekly(): void
    {
        $this->assertSame('0 0 * * 0', Cron::weekly());
    }

    #[Test]
    public function test_weekly_on(): void
    {
        $this->assertSame('0 9 * * 1', Cron::weeklyOn(1, '09:00'));
    }

    #[Test]
    public function test_monthly(): void
    {
        $this->assertSame('0 0 1 * *', Cron::monthly());
    }

    #[Test]
    public function test_monthly_on(): void
    {
        $this->assertSame('0 8 15 * *', Cron::monthlyOn(15, '08:00'));
    }

    #[Test]
    public function test_yearly(): void
    {
        $this->assertSame('0 0 1 1 *', Cron::yearly());
    }

    #[Test]
    public function test_custom_builder(): void
    {
        $expression = Cron::custom()
            ->minute('*/10')
            ->hour('9-17')
            ->dayOfWeek('1-5')
            ->build();

        $this->assertSame('*/10 9-17 * * 1-5', $expression);
    }

    #[Test]
    public function test_validator_valid_expressions(): void
    {
        $this->assertTrue(CronValidator::isValid('* * * * *'));
        $this->assertTrue(CronValidator::isValid('0 0 * * *'));
        $this->assertTrue(CronValidator::isValid('*/5 * * * *'));
        $this->assertTrue(CronValidator::isValid('0 9-17 * * 1-5'));
        $this->assertTrue(CronValidator::isValid('0,30 * * * *'));
        $this->assertTrue(CronValidator::isValid('0 0 1 1 *'));
        $this->assertTrue(CronValidator::isValid('0 0 * * 7'));
    }

    #[Test]
    public function test_validator_invalid_expressions(): void
    {
        $this->assertFalse(CronValidator::isValid(''));
        $this->assertFalse(CronValidator::isValid('* * *'));
        $this->assertFalse(CronValidator::isValid('60 * * * *'));
        $this->assertFalse(CronValidator::isValid('* 25 * * *'));
        $this->assertFalse(CronValidator::isValid('* * 32 * *'));
        $this->assertFalse(CronValidator::isValid('* * * 13 *'));
        $this->assertFalse(CronValidator::isValid('abc * * * *'));
    }

    #[Test]
    public function test_describer_common_patterns(): void
    {
        $this->assertSame('Every minute', CronDescriber::describe('* * * * *'));
        $this->assertSame('Every hour', CronDescriber::describe('0 * * * *'));
        $this->assertSame('Every day at midnight', CronDescriber::describe('0 0 * * *'));
        $this->assertSame('Every Sunday at midnight', CronDescriber::describe('0 0 * * 0'));
        $this->assertSame('First day of every month at midnight', CronDescriber::describe('0 0 1 * *'));
        $this->assertSame('Every year on January 1st at midnight', CronDescriber::describe('0 0 1 1 *'));
        $this->assertSame('Every 5 minutes', CronDescriber::describe('*/5 * * * *'));
        $this->assertSame('Every day at 14:30', CronDescriber::describe('30 14 * * *'));
    }

    #[Test]
    public function test_invalid_time_format_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Cron::dailyAt('invalid');
    }

    #[Test]
    public function test_out_of_range_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Cron::hourlyAt(60);
    }

    #[Test]
    public function test_every_quarter_hour(): void
    {
        $expression = Cron::custom()
            ->everyQuarterHour()
            ->build();

        $this->assertSame('*/15 * * * *', $expression);
    }

    #[Test]
    public function test_every_half_hour(): void
    {
        $expression = Cron::custom()
            ->everyHalfHour()
            ->build();

        $this->assertSame('*/30 * * * *', $expression);
    }

    #[Test]
    public function test_every_quarter_hour_with_other_fields(): void
    {
        $expression = Cron::custom()
            ->everyQuarterHour()
            ->hour('9-17')
            ->dayOfWeek('1-5')
            ->build();

        $this->assertSame('*/15 9-17 * * 1-5', $expression);
    }

    #[Test]
    public function test_every_half_hour_with_other_fields(): void
    {
        $expression = Cron::custom()
            ->everyHalfHour()
            ->hour('8-20')
            ->build();

        $this->assertSame('*/30 8-20 * * *', $expression);
    }

    #[Test]
    public function test_weekly_on_day_all_days(): void
    {
        $days = [
            'Sunday' => '0',
            'Monday' => '1',
            'Tuesday' => '2',
            'Wednesday' => '3',
            'Thursday' => '4',
            'Friday' => '5',
            'Saturday' => '6',
        ];

        foreach ($days as $name => $number) {
            $expression = Cron::custom()
                ->minute('0')
                ->hour('0')
                ->weeklyOnDay($name)
                ->build();

            $this->assertSame("0 0 * * {$number}", $expression, "Failed for day: {$name}");
        }
    }

    #[Test]
    public function test_weekly_on_day_case_insensitive(): void
    {
        $variants = ['monday', 'MONDAY', 'Monday', 'mOnDaY'];

        foreach ($variants as $variant) {
            $expression = Cron::custom()
                ->minute('0')
                ->hour('9')
                ->weeklyOnDay($variant)
                ->build();

            $this->assertSame('0 9 * * 1', $expression, "Failed for variant: {$variant}");
        }
    }

    #[Test]
    public function test_weekly_on_day_invalid_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Cron::custom()->weeklyOnDay('Notaday');
    }

    #[Test]
    public function test_next_run_date_daily(): void
    {
        $expression = new CronExpression('0 0 * * *');
        $from = new \DateTimeImmutable('2026-03-22 10:00:00');
        $next = $expression->nextRunDate($from);

        $this->assertSame('2026-03-23 00:00', $next->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_run_date_weekly(): void
    {
        $expression = new CronExpression('0 9 * * 1');
        // 2026-03-22 is a Sunday
        $from = new \DateTimeImmutable('2026-03-22 10:00:00');
        $next = $expression->nextRunDate($from);

        $this->assertSame('2026-03-23 09:00', $next->format('Y-m-d H:i'));
        $this->assertSame('Monday', $next->format('l'));
    }

    #[Test]
    public function test_next_run_date_monthly(): void
    {
        $expression = new CronExpression('0 0 1 * *');
        $from = new \DateTimeImmutable('2026-03-15 12:00:00');
        $next = $expression->nextRunDate($from);

        $this->assertSame('2026-04-01 00:00', $next->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_run_date_with_specific_from(): void
    {
        $expression = new CronExpression('30 14 * * *');
        $from = new \DateTimeImmutable('2026-06-15 14:00:00');
        $next = $expression->nextRunDate($from);

        $this->assertSame('2026-06-15 14:30', $next->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_run_date_every_five_minutes(): void
    {
        $expression = new CronExpression('*/5 * * * *');
        $from = new \DateTimeImmutable('2026-03-22 10:03:00');
        $next = $expression->nextRunDate($from);

        $this->assertSame('2026-03-22 10:05', $next->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_run_date_defaults_to_now(): void
    {
        $expression = new CronExpression('* * * * *');
        $next = $expression->nextRunDate();

        $now = new \DateTimeImmutable('now');
        $diff = $next->getTimestamp() - $now->getTimestamp();

        $this->assertGreaterThanOrEqual(0, $diff);
        $this->assertLessThanOrEqual(120, $diff);
    }
}
