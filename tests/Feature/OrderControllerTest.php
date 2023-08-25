<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected string $token;

    public function test_orders_can_be_listed(): void
    {
        $this->loginAsAdmin();
        Order::factory(10)->create();

        $response = $this->withToken($this->token)->getJson(route('api.v1.order'));
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'amount', 'created_at', 'delivery_fee', 'products', 'shipped_at', 'uuid', 'order_status', 'user'
                    ]
                ]
            ]);
    }

    public function test_dashboard_orders_can_be_listed(): void
    {
        $this->loginAsAdmin();
        Order::factory(10)->create();

        $response = $this->withToken($this->token)->getJson(route('api.v1.order.dashboard'));
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'amount', 'created_at', 'delivery_fee', 'products', 'shipped_at', 'uuid', 'order_status', 'user'
                    ]
                ]
            ]);
    }

    public function test_order_can_be_stored(): void
    {
        $user = $this->loginAsUser();
        $orderStatus = OrderStatus::factory()->create();
        $payment = Payment::factory()->create();
        /** @var array<Product> $products */
        $products = Product::factory(2)->create();

        $response = $this->withToken($this->token)->postJson(route('api.v1.order.create'), [
            'order_status_uuid' => $orderStatus->uuid,
            'payment_uuid' => $payment->uuid,
            'products' => [
                ['product' => $products[0]->uuid, 'quantity' => 2],
                ['product' => $products[1]->uuid, 'quantity' => 1],
            ],
            'address' => ['shipping' => $this->faker->address, 'billing' => $this->faker->address],
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['uuid']
        ]);

        $this->assertDatabaseHas('orders', [
            'order_status_id' => $orderStatus->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_order_can_be_shown(): void
    {
        $this->loginAsAdmin();
        $order = Order::factory()->create();

        $response = $this->withToken($this->token)->getJson(route('api.v1.order.show', $order));

        $response->assertOk()
            ->assertJson([
                'data' => ['uuid' => $order->uuid]
            ]);
    }

    public function test_order_can_be_updated(): void
    {
        $this->loginAsAdmin();
        $order = Order::factory()->create();
        $newOrderStatus = OrderStatus::factory()->create();
        $newPayment = Payment::factory()->create();
        /** @var array<Product> $products */
        $products = Product::factory(2)->create();

        $response = $this->withToken($this->token)->putJson(route('api.v1.order.edit', $order), [
            'order_status_uuid' => $newOrderStatus->uuid,
            'payment_uuid' => $newPayment->uuid,
            'products' => [
                ['product' => $products[0]->uuid, 'quantity' => 2],
                ['product' => $products[1]->uuid, 'quantity' => 1],
            ],
            'address' => $order->address,
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'order_status' => ['uuid' => $newOrderStatus->uuid]
            ]]);

        $this->assertDatabaseHas('orders', [
            'uuid' => $order->uuid,
            'order_status_id' => $newOrderStatus->id,
            'payment_id' => $newPayment->id,
        ]);
    }

    public function test_order_can_be_deleted(): void
    {
        $this->loginAsAdmin();
        $order = Order::factory()->create();

        $response = $this->withToken($this->token)->deleteJson(route('api.v1.order.delete', $order));

        $response->assertOk()
            ->assertJson(['success' => 1]);

        $this->assertDatabaseMissing('orders', ['uuid' => $order->uuid]);
    }

    public function test_download_order_invoice(): void
    {
        $this->loginAsAdmin();

        $order = Order::factory()->create();

        $response = $this->withToken($this->token)->getJson(route('api.v1.order.download', $order));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_shipped_orders_can_be_listed(): void
    {
        $this->loginAsAdmin();
        $shippedOrderStatus = OrderStatus::factory()->create(['title' => 'shipped']);
        Order::factory(2)->create();
        Order::factory(3)->create(['order_status_id' => $shippedOrderStatus->id]);

        $response = $this->withToken($this->token)->getJson(route('api.v1.order.shipped'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'amount', 'created_at', 'delivery_fee', 'products', 'shipped_at', 'uuid', 'order_status', 'user', 'payment'
                    ]
                ]
            ]);
    }

    private function loginAsAdmin(): void
    {
        $admin = User::factory([
            'is_admin' => true
        ])->create();
        $this->token = $admin->createToken('test-admin-auth');
        return;
    }

    private function loginAsUser(): User
    {
        $user = User::factory()->create();
        $this->token = $user->createToken('test-user-auth');
        return $user;
    }
}
