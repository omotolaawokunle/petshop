<?php

namespace App\Services\Traits;

use App\Services\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, BaseFilter $filter): Builder
    {
        return $filter->apply($query);
    }
}
