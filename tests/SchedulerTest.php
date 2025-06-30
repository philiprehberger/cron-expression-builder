<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder\Tests;

use PhilipRehberger\CronBuilder\CronExpression;
use PhilipRehberger\CronBuilder\CronScheduler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SchedulerTest extends TestCase
{
    #[Test]
    public function test_next_runs_daily_at_nine_returns_consecutive_days(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 10:00:00');
        $runs = CronScheduler::nextRuns('0 9 * * *', 5, $from);

        $this->assertCount(5, $runs);
        $this->assertSame('2026-03-23 09:00', $runs[0]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-24 09:00', $runs[1]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-25 09:00', $runs[2]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-26 09:00', $runs[3]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-27 09:00', $runs[4]->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_runs_every_five_minutes_returns_five_minute_intervals(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 10:00:00');
        $runs = CronScheduler::nextRuns('*/5 * * * *', 5, $from);

        $this->assertCount(5, $runs);
        $this->assertSame('2026-03-22 10:05', $runs[0]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-22 10:10', $runs[1]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-22 10:15', $runs[2]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-22 10:20', $runs[3]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-22 10:25', $runs[4]->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_runs_with_custom_from_date(): void
    {
        $from = new \DateTimeImmutable('2026-06-15 14:00:00');
        $runs = CronScheduler::nextRuns('30 14 * * *', 3, $from);

        $this->assertCount(3, $runs);
        $this->assertSame('2026-06-15 14:30', $runs[0]->format('Y-m-d H:i'));
        $this->assertSame('2026-06-16 14:30', $runs[1]->format('Y-m-d H:i'));
        $this->assertSame('2026-06-17 14:30', $runs[2]->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_runs_with_count_one(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 10:00:00');
        $runs = CronScheduler::nextRuns('0 0 * * *', 1, $from);

        $this->assertCount(1, $runs);
        $this->assertSame('2026-03-23 00:00', $runs[0]->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_runs_monthly_expression(): void
    {
        $from = new \DateTimeImmutable('2026-01-15 12:00:00');
        $runs = CronScheduler::nextRuns('0 0 1 * *', 3, $from);

        $this->assertCount(3, $runs);
        $this->assertSame('2026-02-01 00:00', $runs[0]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-01 00:00', $runs[1]->format('Y-m-d H:i'));
        $this->assertSame('2026-04-01 00:00', $runs[2]->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_runs_accepts_cron_expression_object(): void
    {
        $expr = new CronExpression('0 9 * * *');
        $from = new \DateTimeImmutable('2026-03-22 10:00:00');
        $runs = CronScheduler::nextRuns($expr, 2, $from);

        $this->assertCount(2, $runs);
        $this->assertSame('2026-03-23 09:00', $runs[0]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-24 09:00', $runs[1]->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_runs_convenience_method_on_cron_expression(): void
    {
        $expr = new CronExpression('0 9 * * *');
        $from = new \DateTimeImmutable('2026-03-22 10:00:00');
        $runs = $expr->nextRuns(3, $from);

        $this->assertCount(3, $runs);
        $this->assertSame('2026-03-23 09:00', $runs[0]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-24 09:00', $runs[1]->format('Y-m-d H:i'));
        $this->assertSame('2026-03-25 09:00', $runs[2]->format('Y-m-d H:i'));
    }

    #[Test]
    public function test_next_runs_throws_for_zero_count(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CronScheduler::nextRuns('* * * * *', 0);
    }
}
