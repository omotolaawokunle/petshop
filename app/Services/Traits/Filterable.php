<?php

namespace App\Services\Traits;

use App\Services\Filters\BaseFilter;

trait Filterable
{
    public function scopeFilter($query, BaseFilter $filter)
    {
        return $filter->apply($query);
    }
}
