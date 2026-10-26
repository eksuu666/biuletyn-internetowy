<?php

namespace Modules\Common\App\Helpers;

use Illuminate\Support\Carbon;

class JalalianHelper
{
    private ?Carbon $date = null;

    public function forge($date): self
    {
        $this->date = $date ? Carbon::parse($date)->locale(app()->getLocale()) : null;
        return $this;
    }

    public function now(): self
    {
        $this->date = Carbon::now()->locale(app()->getLocale());
        return $this;
    }

    public function format(string $format = 'd.m.Y H:i'): string
    {
        if (! $this->date) {
            return '-';
        }
        $format = str_replace(
            ['Y/m/d H:i:s', 'Y/m/d H:i', 'Y/m/d', 'l، Y/m/d'],
            ['d.m.Y H:i', 'd.m.Y H:i', 'd.m.Y', 'l, d.m.Y'],
            $format
        );
        return $this->date->translatedFormat($format);
    }

    public function ago(): string
    {
        if (! $this->date) {
            return '-';
        }
        return $this->date->diffForHumans();
    }

    public function __call($method, $args)
    {
        if ($this->date && method_exists($this->date, $method)) {
            return call_user_func_array([$this->date, $method], $args);
        }
        return '-';
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
