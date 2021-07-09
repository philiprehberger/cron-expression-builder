<?php

declare(strict_types=1);

namespace PhilipRehberger\CronBuilder;

use Stringable;

final class CronBuilder implements Stringable
{
    private string $minute = '*';

    private string $hour = '*';

    private string $dayOfMonth = '*';

    private string $month = '*';

    private string $dayOfWeek = '*';

    public function minute(string $expr): self
    {
        $this->minute = $expr;

        return $this;
    }

    public function hour(string $expr): self
    {
        $this->hour = $expr;

        return $this;
    }

    public function dayOfMonth(string $expr): self
    {
        $this->dayOfMonth = $expr;

        return $this;
    }

    public function month(string $expr): self
    {
        $this->month = $expr;

        return $this;
    }

    public function dayOfWeek(string $expr): self
    {
        $this->dayOfWeek = $expr;

        return $this;
    }

    public function everyQuarterHour(): self
    {
        $this->minute = '*/15';

        return $this;
    }

    public function everyHalfHour(): self
    {
        $this->minute = '*/30';

        return $this;
    }

    public function weeklyOnDay(string $dayName): self
    {
        $map = [
            'sunday' => '0',
            'monday' => '1',
            'tuesday' => '2',
            'wednesday' => '3',
            'thursday' => '4',
            'friday' => '5',
            'saturday' => '6',
        ];

        $key = strtolower($dayName);

        if (! isset($map[$key])) {
            throw new \InvalidArgumentException("Invalid day name '{$dayName}'.");
        }

        $this->dayOfWeek = $map[$key];

        return $this;
    }

    public function build(): string
    {
        return implode(' ', [
            $this->minute,
            $this->hour,
            $this->dayOfMonth,
            $this->month,
            $this->dayOfWeek,
        ]);
    }

    public function __toString(): string
    {
        return $this->build();
    }
}
