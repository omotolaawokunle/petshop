<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Filters\UserFilter;

class UserFilterTest extends TestCase
{
    public function test_first_name_filter(): void
    {
        $request = new Request(['first_name' => 'John']);
        $filter = new UserFilter($request);
        $query = User::query();
        $filter->apply($query);
        $this->assertEquals("select * from `users` where `first_name` LIKE '%John%'", $query->toRawSql());
    }
}
