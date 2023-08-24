<?php

namespace App\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends BaseFilter
{
    public function category(string $term): Builder
    {
        return $this->builder->where('category_uuid', $term);
    }

    public function price(string $term): Builder
    {
        return $this->builder->where('price', '<', $term);
    }

    public function brand(string $term): Builder
    {
        return $this->builder->where('metadata->brand', $term);
    }

    public function title(string $term): Builder
    {
        return $this->builder->where('title', 'LIKE', "%{$term}%");
    }
}
