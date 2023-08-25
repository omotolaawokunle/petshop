<?php

namespace App\Services\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderFilter extends BaseFilter
{
    public function categoryUuid(string $term): Builder
    {
        return $this->builder->whereHas('category', function ($query) use ($term) {
            return $query->where('categories.uuid', $term);
        });
    }

    public function orderUuid(string $term): Builder
    {
        return $this->builder->where('orders.uuid', $term);
    }

    /**
     * @param  array<string> $range
     * @return Builder
     */
    public function dateRange(array $range): Builder
    {
        if (isset($range['from']) && $range['from']) {
            $this->builder->whereDate('created_at', '>=', Carbon::parse($range['from']));
        }

        if (isset($range['to']) && $range['to']) {
            $this->builder->whereDate('created_at', '<=', Carbon::parse($range['to']));
        }

        return $this->builder;
    }

    public function fixRange(string $term): Builder
    {
        switch ($term) {
            case 'today':
                return $this->builder->whereDate('created_at', today());
            case 'monthly':
                return $this->builder
                    ->whereMonth('created_at', today())->whereYear('created_at', today());
            case 'yearly':
                return $this->builder->whereYear('created_at', today());
            default:
                return $this->builder;
        }
    }
}
