<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Services\Filters\BaseFilter;

class BaseFilterTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_filters_method_returns_all_request_parameters(): void
    {
        $request = new Request(['name' => 'John', 'email' => 'john@laravel.com']);
        $filter = new BaseFilter($request);

        $this->assertEquals(['name' => 'John', 'email' => 'john@laravel.com'], $filter->filters());
    }
}
