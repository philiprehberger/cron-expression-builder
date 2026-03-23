<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder;

final class CronExpression
{
    private string $minute;

    private string $hour;

    private string $dayOfMonth;

    private string $month;

    private string $dayOfWeek;

    public function __construct(string $expression)
    {
        if (! CronValidator::isValid($expression)) {
            throw new \InvalidArgumentException("Invalid cron expression '{$expression}'.");
        }

        $parts = preg_split('/\s+/', trim($expression));

        if ($parts === false || count($parts) !== 5) {
            throw new \InvalidArgumentException("Invalid cron expression '{$expression}'.");
        }

        [$this->minute, $this->hour, $this->dayOfMonth, $this->month, $this->dayOfWeek] = $parts;
    }

    /**
     * Calculate the next run date from the given datetime (default: now).
     */
    public function nextRunDate(?\DateTimeInterface $from = null): \DateTimeImmutable
    {
        $current = $from !== null
            ? \DateTimeImmutable::createFromInterface($from)
            : new \DateTimeImmutable('now');

        // Start from the next minute (zero out seconds)
        $current = $current->setTime(
            (int) $current->format('H'),
            (int) $current->format('i'),
            0
        )->modify('+1 minute');

        // Limit to 1 year of searching (525960 minutes)
        $limit = 525960;

        for ($i = 0; $i < $limit; $i++) {
            if ($this->matches($current)) {
                return $current;
            }

            $current = $current->modify('+1 minute');
        }

        throw new \RuntimeException('Unable to find next run date within one year.');
    }

    private function matches(\DateTimeImmutable $date): bool
    {
        $minute = (int) $date->format('i');
        $hour = (int) $date->format('G');
        $dayOfMonth = (int) $date->format('j');
        $month = (int) $date->format('n');
        $dayOfWeek = (int) $date->format('w');

        return $this->matchesField($this->minute, $minute, 0, 59)
            && $this->matchesField($this->hour, $hour, 0, 23)
            && $this->matchesField($this->dayOfMonth, $dayOfMonth, 1, 31)
            && $this->matchesField($this->month, $month, 1, 12)
            && $this->matchesDayOfWeek($dayOfWeek);
    }

    private function matchesDayOfWeek(int $value): bool
    {
        // Normalize: 7 is also Sunday (0)
        $segments = explode(',', $this->dayOfWeek);

        foreach ($segments as $segment) {
            if ($this->segmentMatches($segment, $value, 0, 7)) {
                return true;
            }

            // Also match Sunday as 7 if value is 0, or as 0 if value is 7
            if ($value === 0 && $this->segmentMatches($segment, 7, 0, 7)) {
                return true;
            }
        }

        return false;
    }

    private function matchesField(string $field, int $value, int $min, int $max): bool
    {
        $segments = explode(',', $field);

        foreach ($segments as $segment) {
            if ($this->segmentMatches($segment, $value, $min, $max)) {
                return true;
            }
        }

        return false;
    }

    private function segmentMatches(string $segment, int $value, int $min, int $max): bool
    {
        // Wildcard
        if ($segment === '*') {
            return true;
        }

        // Step values: */N or N-M/N
        if (str_contains($segment, '/')) {
            $parts = explode('/', $segment, 2);
            $step = (int) $parts[1];
            $base = $parts[0];

            if ($base === '*') {
                return ($value - $min) % $step === 0;
            }

            if (str_contains($base, '-')) {
                [$rangeMin, $rangeMax] = array_map('intval', explode('-', $base, 2));

                return $value >= $rangeMin && $value <= $rangeMax && ($value - $rangeMin) % $step === 0;
            }

            return $value === (int) $base;
        }

        // Range: N-M
        if (str_contains($segment, '-')) {
            [$rangeMin, $rangeMax] = array_map('intval', explode('-', $segment, 2));

            return $value >= $rangeMin && $value <= $rangeMax;
        }

        // Single number
        return $value === (int) $segment;
    }
}
