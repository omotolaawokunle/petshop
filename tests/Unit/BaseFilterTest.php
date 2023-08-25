<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\Filters\BaseFilter;

class BaseFilterTest extends TestCase
{
    public function test_filters_method_returns_all_request_parameters(): void
    {
        $request = new Request(['name' => 'John', 'email' => 'john@laravel.com']);
        $filter = new BaseFilter($request);

        $this->assertEquals(['name' => 'John', 'email' => 'john@laravel.com'], $filter->filters());
    }

    public function test_sort_by_filter(): void
    {
        $request = new Request(['sortBy' => 'name', 'desc' => 1]);
        $filter = new BaseFilter($request);
        $query = $filter->apply(User::query());
        /** @var string $sql */
        $sql = $query->toRawSql();
        $this->assertTrue(Str::contains($sql, "select * from `users` order by `name` desc"));
    }
}
