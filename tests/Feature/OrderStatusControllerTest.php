<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderStatusControllerTest extends TestCase
{
    use DatabaseTransactions;
    public function test_get_all_statuses(): void
    {
        $response = $this->getJson(route('api.v1.order-status'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid', 'title', 'created_at', 'updated_at'
                ]
            ]
        ]);
    }
}
