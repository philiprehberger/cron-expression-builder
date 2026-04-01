<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder\Tests;

use PhilipRehberger\CronBuilder\CronExpression;
use PhilipRehberger\CronBuilder\CronInterval;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IntervalTest extends TestCase
{
    #[Test]
    public function test_average_interval_every_five_minutes(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');
        $avg = CronInterval::averageInterval('*/5 * * * *', 100, $from);

        $this->assertEqualsWithDelta(300.0, $avg, 1.0);
    }

    #[Test]
    public function test_min_interval_daily(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');
        $min = CronInterval::minInterval('0 0 * * *', 100, $from);

        $this->assertEqualsWithDelta(86400.0, $min, 1.0);
    }

    #[Test]
    public function test_max_interval_daily(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');
        $max = CronInterval::maxInterval('0 0 * * *', 100, $from);

        $this->assertEqualsWithDelta(86400.0, $max, 1.0);
    }

    #[Test]
    public function test_average_interval_hourly(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');
        $avg = CronInterval::averageInterval('0 * * * *', 50, $from);

        $this->assertEqualsWithDelta(3600.0, $avg, 1.0);
    }

    #[Test]
    public function test_min_interval_every_minute(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');
        $min = CronInterval::minInterval('* * * * *', 50, $from);

        $this->assertEqualsWithDelta(60.0, $min, 1.0);
    }

    #[Test]
    public function test_intervals_with_cron_expression_object(): void
    {
        $expr = new CronExpression('*/10 * * * *');
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');

        $avg = CronInterval::averageInterval($expr, 50, $from);
        $min = CronInterval::minInterval($expr, 50, $from);
        $max = CronInterval::maxInterval($expr, 50, $from);

        $this->assertEqualsWithDelta(600.0, $avg, 1.0);
        $this->assertEqualsWithDelta(600.0, $min, 1.0);
        $this->assertEqualsWithDelta(600.0, $max, 1.0);
    }

    #[Test]
    public function test_sample_size_of_one_returns_zero(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');
        $avg = CronInterval::averageInterval('* * * * *', 1, $from);

        $this->assertSame(0.0, $avg);
    }
}
