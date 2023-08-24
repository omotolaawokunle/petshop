<?php

namespace App\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter extends BaseFilter
{
    public function firstName(string $term): Builder
    {
        return $this->builder->where('first_name', 'LIKE', "%{$term}%");
    }

    public function email(string $term): Builder
    {
        return $this->builder->where('email', 'LIKE', "%{$term}%");
    }

    public function phone(string $term): Builder
    {
        return $this->builder->where('phone', 'LIKE', "%{$term}%");
    }

    public function address(string $term): Builder
    {
        return $this->builder->where('address', 'LIKE', "%{$term}%");
    }

    public function createdAt(string $term): Builder
    {
        return $this->builder->whereDate('created_at', $term);
    }

    public function marketing(mixed $term): Builder
    {
        $term = (bool) $term;
        return $this->builder->where('is_marketing', $term);
    }
}
