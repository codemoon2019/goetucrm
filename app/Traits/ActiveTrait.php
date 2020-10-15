<?php

namespace App\Traits;

trait ActiveTrait
{
    public function scopeIsActive($query)
    {
        return $query->where('status', '<>', 'D');
    }
}