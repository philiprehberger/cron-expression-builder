<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder\Tests;

use PhilipRehberger\CronBuilder\CronScheduler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OverlapTest extends TestCase
{
    #[Test]
    public function test_same_time_expressions_overlap(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');

        $this->assertTrue(
            CronScheduler::overlaps('0 9 * * *', '0 9 * * *', 24, $from)
        );
    }

    #[Test]
    public function test_different_time_expressions_do_not_overlap(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');

        $this->assertFalse(
            CronScheduler::overlaps('0 9 * * *', '0 14 * * *', 24, $from)
        );
    }

    #[Test]
    public function test_overlapping_step_expressions(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');

        // */10 and */15 both fire at :00 and :30
        $this->assertTrue(
            CronScheduler::overlaps('*/10 * * * *', '*/15 * * * *', 1, $from)
        );
    }

    #[Test]
    public function test_find_overlaps_with_multiple_expressions(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');

        $expressions = [
            'a' => '0 9 * * *',
            'b' => '0 9 * * *',
            'c' => '0 14 * * *',
        ];

        $overlaps = CronScheduler::findOverlaps($expressions, 24, $from);

        $this->assertNotEmpty($overlaps);

        $firstOverlap = $overlaps[0];
        $this->assertContains('a', $firstOverlap['expressions']);
        $this->assertContains('b', $firstOverlap['expressions']);
        $this->assertSame('09:00', $firstOverlap['time']->format('H:i'));
    }

    #[Test]
    public function test_find_overlaps_returns_empty_when_no_overlaps(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');

        $expressions = [
            '0 9 * * *',
            '0 14 * * *',
            '0 20 * * *',
        ];

        $overlaps = CronScheduler::findOverlaps($expressions, 24, $from);

        $this->assertEmpty($overlaps);
    }

    #[Test]
    public function test_find_overlaps_sorted_by_time(): void
    {
        $from = new \DateTimeImmutable('2026-03-22 00:00:00');

        $expressions = [
            '0 14 * * *',
            '0 9 * * *',
            '0 14 * * *',
            '0 9 * * *',
        ];

        $overlaps = CronScheduler::findOverlaps($expressions, 24, $from);

        $this->assertGreaterThanOrEqual(2, count($overlaps));

        for ($i = 1; $i < count($overlaps); $i++) {
            $this->assertGreaterThanOrEqual(
                $overlaps[$i - 1]['time']->getTimestamp(),
                $overlaps[$i]['time']->getTimestamp()
            );
        }
    }
}
