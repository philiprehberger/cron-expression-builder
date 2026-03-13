<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder;

final class CronValidator
{
    /** @var array<int, array{int, int}> */
    private const FIELD_RANGES = [
        [0, 59],  // minute
        [0, 23],  // hour
        [1, 31],  // day of month
        [1, 12],  // month
        [0, 7],   // day of week (0 and 7 both = Sunday)
    ];

    public static function isValid(string $expression): bool
    {
        $parts = preg_split('/\s+/', trim($expression));

        if ($parts === false || count($parts) !== 5) {
            return false;
        }

        foreach ($parts as $index => $part) {
            if (! self::isValidField($part, self::FIELD_RANGES[$index])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array{int, int}  $range
     */
    private static function isValidField(string $field, array $range): bool
    {
        $segments = explode(',', $field);

        foreach ($segments as $segment) {
            if (! self::isValidSegment($segment, $range)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array{int, int}  $range
     */
    private static function isValidSegment(string $segment, array $range): bool
    {
        // Wildcard
        if ($segment === '*') {
            return true;
        }

        // Step values: */N or N-M/N
        if (str_contains($segment, '/')) {
            $parts = explode('/', $segment, 2);
            $step = $parts[1];

            if (! ctype_digit($step) || (int) $step < 1) {
                return false;
            }

            $base = $parts[0];

            if ($base === '*') {
                return true;
            }

            return self::isValidRange($base, $range);
        }

        // Range: N-M
        if (str_contains($segment, '-')) {
            return self::isValidRange($segment, $range);
        }

        // Single number
        if (! ctype_digit($segment)) {
            return false;
        }

        $num = (int) $segment;

        return $num >= $range[0] && $num <= $range[1];
    }

    /**
     * @param  array{int, int}  $range
     */
    private static function isValidRange(string $rangeStr, array $range): bool
    {
        $parts = explode('-', $rangeStr, 2);

        if (count($parts) !== 2) {
            return false;
        }

        if (! ctype_digit($parts[0]) || ! ctype_digit($parts[1])) {
            return false;
        }

        $min = (int) $parts[0];
        $max = (int) $parts[1];

        return $min >= $range[0] && $max <= $range[1] && $min <= $max;
    }
}
