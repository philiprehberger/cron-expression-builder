<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder;

use DateTimeImmutable;
use DateTimeInterface;

final class CronScheduler
{
    /**
     * Calculate the next N run dates for a cron expression.
     *
     * @param  string|CronExpression  $expression  A cron expression string or CronExpression instance
     * @param  int  $count  Number of run dates to return
     * @param  DateTimeInterface|null  $from  Starting datetime (default: now)
     * @return array<int, DateTimeImmutable>
     */
    public static function nextRuns(string|CronExpression $expression, int $count = 5, ?DateTimeInterface $from = null): array
    {
        if ($count < 1) {
            throw new \InvalidArgumentException('Count must be at least 1.');
        }

        $expr = $expression instanceof CronExpression
            ? $expression
            : new CronExpression($expression);

        $results = [];
        $current = $from;

        for ($i = 0; $i < $count; $i++) {
            $next = $expr->nextRunDate($current);
            $results[] = $next;
            $current = $next;
        }

        return $results;
    }

    /**
     * Check if two cron expressions produce overlapping execution times within a time window.
     *
     * @param  string|CronExpression  $a  First cron expression
     * @param  string|CronExpression  $b  Second cron expression
     * @param  int  $windowHours  Number of hours to check (default: 24)
     * @param  DateTimeInterface|null  $from  Starting datetime (default: now)
     */
    public static function overlaps(string|CronExpression $a, string|CronExpression $b, int $windowHours = 24, ?DateTimeInterface $from = null): bool
    {
        $fromDate = $from !== null
            ? DateTimeImmutable::createFromInterface($from)
            : new DateTimeImmutable('now');

        $windowEnd = $fromDate->modify("+{$windowHours} hours");

        $runsA = self::collectRunsInWindow($a, $fromDate, $windowEnd);
        $runsB = self::collectRunsInWindow($b, $fromDate, $windowEnd);

        foreach ($runsA as $timestamp) {
            if (isset($runsB[$timestamp])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find all overlapping execution times among multiple cron expressions.
     *
     * @param  array<int|string, string|CronExpression>  $expressions  Array of cron expressions
     * @param  int  $windowHours  Number of hours to check (default: 24)
     * @param  DateTimeInterface|null  $from  Starting datetime (default: now)
     * @return array<int, array{expressions: array<int, int|string>, time: DateTimeImmutable}>
     */
    public static function findOverlaps(array $expressions, int $windowHours = 24, ?DateTimeInterface $from = null): array
    {
        $fromDate = $from !== null
            ? DateTimeImmutable::createFromInterface($from)
            : new DateTimeImmutable('now');

        $windowEnd = $fromDate->modify("+{$windowHours} hours");

        /** @var array<int|string, array<int, int>> */
        $allRuns = [];

        foreach ($expressions as $key => $expr) {
            $allRuns[$key] = self::collectRunsInWindow($expr, $fromDate, $windowEnd);
        }

        /** @var array<int, array<int, int|string>> */
        $timestampToKeys = [];

        foreach ($allRuns as $key => $runs) {
            foreach ($runs as $timestamp => $value) {
                $timestampToKeys[$timestamp][] = $key;
            }
        }

        $overlaps = [];

        foreach ($timestampToKeys as $timestamp => $keys) {
            if (count($keys) > 1) {
                $overlaps[] = [
                    'expressions' => $keys,
                    'time' => (new DateTimeImmutable)->setTimestamp($timestamp),
                ];
            }
        }

        usort($overlaps, fn (array $a, array $b) => $a['time']->getTimestamp() <=> $b['time']->getTimestamp());

        return $overlaps;
    }

    /**
     * Collect all run timestamps within a time window for a cron expression.
     *
     * @return array<int, int> Map of timestamp => timestamp
     */
    private static function collectRunsInWindow(string|CronExpression $expression, DateTimeImmutable $from, DateTimeImmutable $windowEnd): array
    {
        $expr = $expression instanceof CronExpression
            ? $expression
            : new CronExpression($expression);

        $runs = [];
        $current = $from;

        while (true) {
            try {
                $next = $expr->nextRunDate($current);
            } catch (\RuntimeException) {
                break;
            }

            if ($next > $windowEnd) {
                break;
            }

            $timestamp = $next->getTimestamp();
            $runs[$timestamp] = $timestamp;
            $current = $next;
        }

        return $runs;
    }
}
