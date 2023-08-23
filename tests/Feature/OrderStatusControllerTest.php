<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\OrderStatus;

class OrderStatusControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    protected $token;

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

    public function test_get_all_statuses_with_sort(): void
    {
        $response = $this->getJson(route('api.v1.order-status'), ['sortBy' => 'title', 'desc' => 0]);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid', 'title', 'created_at', 'updated_at'
                ]
            ]
        ]);
    }

    public function test_authenticated_user_can_create_order_status(): void
    {
        $this->loginAsUser();

        $data = [
            'title' => $this->faker->word,
        ];

        $response = $this->withToken($this->token)->postJson(route('api.v1.order-status.create'), $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['uuid']
        ]);
    }

    public function test_retrieve_order_status(): void
    {
        $orderStatus = $this->getOrderStatus();
        $response = $this->getJson(route('api.v1.order-status.show', $orderStatus));

        $response->assertOk();
        $response->assertJson([
            'data' => ['uuid' => $orderStatus->uuid]
        ]);
    }

    public function test_authenticated_user_can_update_order_status(): void
    {
        $this->loginAsUser();

        $orderStatus = $this->getOrderStatus();

        $updatedData = [
            'title' => $this->faker->word,
        ];

        $response = $this->withToken($this->token)->putJson(route('api.v1.order-status.edit', $orderStatus), $updatedData);

        $response->assertOk();
        $response->assertJson(['data' => $updatedData]);
    }

    public function test_authenticated_user_can_destroy_order_status(): void
    {
        $this->loginAsUser();
        $orderStatus = $this->getOrderStatus();

        $response = $this->withToken($this->token)->deleteJson(route('api.v1.order-status.delete', $orderStatus));

        $response->assertOk();
        $response->assertJson(['data' => []]);

        $this->assertDatabaseMissing('order_statuses', ['title' => $orderStatus->title]);
    }

    public function test_unauthenticated_user_cannot_access_protected_methods(): void
    {
        $orderStatus = $this->getOrderStatus();

        // Testing store
        $response = $this->postJson(route('api.v1.order-status.create'));
        $response->assertUnauthorized();

        // Testing update
        $response = $this->putJson(route('api.v1.order-status.edit', $orderStatus));
        $response->assertUnauthorized();

        // Testing destroy
        $response = $this->deleteJson(route('api.v1.order-status.delete', $orderStatus));
        $response->assertUnauthorized();
    }

    private function loginAsUser(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->token = $user->createToken('test-user-auth');
        return;
    }

    private function getOrderStatus(): OrderStatus
    {
        return OrderStatus::create(['title' => $this->faker->word]);
    }
}
