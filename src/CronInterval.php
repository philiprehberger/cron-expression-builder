<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder;

use DateTimeInterface;

final class CronInterval
{
    /**
     * Calculate the average interval between runs in seconds.
     *
     * @param  string|CronExpression  $expression  A cron expression string or CronExpression instance
     * @param  int  $sampleSize  Number of consecutive runs to sample
     * @param  DateTimeInterface|null  $from  Starting datetime (default: now)
     */
    public static function averageInterval(string|CronExpression $expression, int $sampleSize = 100, ?DateTimeInterface $from = null): float
    {
        $intervals = self::calculateIntervals($expression, $sampleSize, $from);

        if (count($intervals) === 0) {
            return 0.0;
        }

        return array_sum($intervals) / count($intervals);
    }

    /**
     * Calculate the minimum interval between runs in seconds.
     *
     * @param  string|CronExpression  $expression  A cron expression string or CronExpression instance
     * @param  int  $sampleSize  Number of consecutive runs to sample
     * @param  DateTimeInterface|null  $from  Starting datetime (default: now)
     */
    public static function minInterval(string|CronExpression $expression, int $sampleSize = 100, ?DateTimeInterface $from = null): float
    {
        $intervals = self::calculateIntervals($expression, $sampleSize, $from);

        if (count($intervals) === 0) {
            return 0.0;
        }

        return (float) min($intervals);
    }

    /**
     * Calculate the maximum interval between runs in seconds.
     *
     * @param  string|CronExpression  $expression  A cron expression string or CronExpression instance
     * @param  int  $sampleSize  Number of consecutive runs to sample
     * @param  DateTimeInterface|null  $from  Starting datetime (default: now)
     */
    public static function maxInterval(string|CronExpression $expression, int $sampleSize = 100, ?DateTimeInterface $from = null): float
    {
        $intervals = self::calculateIntervals($expression, $sampleSize, $from);

        if (count($intervals) === 0) {
            return 0.0;
        }

        return (float) max($intervals);
    }

    /**
     * Calculate intervals between consecutive runs.
     *
     * @return array<int, float>
     */
    private static function calculateIntervals(string|CronExpression $expression, int $sampleSize, ?DateTimeInterface $from): array
    {
        if ($sampleSize < 2) {
            return [];
        }

        $runs = CronScheduler::nextRuns($expression, $sampleSize, $from);
        $intervals = [];

        for ($i = 1; $i < count($runs); $i++) {
            $intervals[] = (float) ($runs[$i]->getTimestamp() - $runs[$i - 1]->getTimestamp());
        }

        return $intervals;
    }
}
