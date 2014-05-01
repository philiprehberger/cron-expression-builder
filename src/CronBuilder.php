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
