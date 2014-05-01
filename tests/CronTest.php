<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder\Tests;

use InvalidArgumentException;
use PhilipRehberger\CronBuilder\Cron;
use PhilipRehberger\CronBuilder\CronDescriber;
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
}
