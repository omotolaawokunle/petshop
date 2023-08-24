<?php

namespace App\Services\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


class ProductFilter extends BaseFilter
{

    public function __construct(protected Request $request)
    {
        parent::__construct($request);
    }

    public function category($term): Builder
    {
        return $this->builder->where('category_uuid', $term);
    }

    public function price($term): Builder
    {
        return $this->builder->where('price', '<', $term);
    }

    public function brand($term)
    {
        return $this->builder->where('metadata->brand', $term);
    }

    public function title($term)
    {
        return $this->builder->where('title', 'LIKE', "%$term%");
    }
}
