<?php

namespace App\Services\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderFilter extends BaseFilter
{
    public function categoryUuid($term): Builder
    {
        return $this->builder->whereHas('category', function ($query) use ($term) {
            return $query->where('categories.uuid', $term);
        });
    }

    public function orderUuid($term): Builder
    {
        return $this->builder->where('orders.uuid', $term);
    }

    public function dateRange(array $range): Builder
    {
        if (isset($range['from']) && !is_null($range['from'])) {
            $this->builder->whereDate('created_at', '>=', Carbon::parse($range['from']));
        }

        if (isset($range['to']) && !is_null($range['to'])) {
            $this->builder->whereDate('created_at', '<=', Carbon::parse($range['to']));
        }

        return $this->builder;
    }

    public function fixRange($term): Builder
    {
        return match ($term) {
            'today' => $this->builder->whereDate('created_at', today()),
            'monthly' => $this->builder->whereMonth('created_at', today())->whereYear('created_at', today()),
            'yearly' => $this->builder->whereYear('created_at', today()),
        };
    }
}
