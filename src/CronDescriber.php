<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder;

final class CronDescriber
{
    /**
     * Describe a cron expression in human-readable text.
     */
    public static function describe(string $expression): string
    {
        if (! CronValidator::isValid($expression)) {
            return 'Invalid cron expression';
        }

        $parts = preg_split('/\s+/', trim($expression));

        if ($parts === false || count($parts) !== 5) {
            return 'Invalid cron expression';
        }

        [$minute, $hour, $dom, $month, $dow] = $parts;

        // Common patterns
        if ($expression === '* * * * *') {
            return 'Every minute';
        }

        if ($minute === '0' && $hour === '*' && $dom === '*' && $month === '*' && $dow === '*') {
            return 'Every hour';
        }

        if ($minute === '0' && $hour === '0' && $dom === '*' && $month === '*' && $dow === '*') {
            return 'Every day at midnight';
        }

        if ($minute === '0' && $hour === '0' && $dom === '*' && $month === '*' && $dow === '0') {
            return 'Every Sunday at midnight';
        }

        if ($minute === '0' && $hour === '0' && $dom === '1' && $month === '*' && $dow === '*') {
            return 'First day of every month at midnight';
        }

        if ($minute === '0' && $hour === '0' && $dom === '1' && $month === '1' && $dow === '*') {
            return 'Every year on January 1st at midnight';
        }

        // Step patterns
        if (str_starts_with($minute, '*/') && $hour === '*' && $dom === '*' && $month === '*' && $dow === '*') {
            $step = substr($minute, 2);

            return "Every {$step} minutes";
        }

        // Time-based
        if (ctype_digit($minute) && ctype_digit($hour) && $dom === '*' && $month === '*' && $dow === '*') {
            return sprintf('Every day at %02d:%02d', (int) $hour, (int) $minute);
        }

        return "Custom schedule: {$expression}";
    }
}
