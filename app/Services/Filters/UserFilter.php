<?php

namespace App\Services\Filters;

use Illuminate\Support\Str;
use Illuminate\Http\Request;


class UserFilter extends BaseFilter
{

    public function __construct(protected Request $request)
    {
        parent::__construct($request);
    }

    public function first_name($term)
    {
        return $this->builder->where('first_name', 'LIKE', "%$term%");
    }

    public function email($term)
    {
        return $this->builder->where('email', 'LIKE', "%$term%");
    }

    public function phone($term)
    {
        return $this->builder->where('phone', 'LIKE', "%$term%");
    }

    public function address($term)
    {
        return $this->builder->where('address', 'LIKE', "%$term%");
    }

    public function created_at($term)
    {
        return $this->builder->whereDate('created_at', $term);
    }

    public function marketing($term)
    {
        $term = (bool) $term;
        return $this->builder->where('is_marketing', $term);
    }
}
