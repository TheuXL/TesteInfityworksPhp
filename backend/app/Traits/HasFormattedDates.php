<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasFormattedDates
{
    public function getFormattedBirthDateAttribute(): ?string
    {
        return $this->birth_date instanceof \Illuminate\Support\Carbon
            ? $this->birth_date->format('d/m/Y')
            : null;
    }
}
