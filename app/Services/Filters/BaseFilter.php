<?php

namespace App\Services\Filters;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class BaseFilter
{
    protected Builder $builder;

    public function __construct(protected Request $request)
    {
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;
        foreach ($this->filters() as $name => $value) {
            $name = Str::camel($name);
            if (!method_exists($this, $name)) {
                continue;
            }

            if (strlen($value)) {
                $this->$name($value);
            } else {
                $this->$name();
            }
        }

        return $this->builder;
    }

    public function filters(): array
    {
        return $this->request->all();
    }

    public function sortBy(string $field): Builder
    {
        try {
            $type = (bool) $this->request->get('desc', 0) ? 'desc' : 'asc';
            return $this->builder->orderBy($field, $type);
        } catch (\Throwable $e) {
            return $this->builder->latest('created_at');
        }
    }
}
