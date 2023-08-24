<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Payment;
use App\Enums\PaymentType;

class PaymentControllerTest extends TestCase
{
    use DatabaseTransactions;
    protected string $token;


    public function test_authenticated_user_can_get_payments_list(): void
    {
        $this->loginAsAdmin();
        Payment::factory()->count(5)->create();

        $response = $this->withToken($this->token)->getJson(route('api.v1.payment'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['uuid', 'type', 'details'],
                ],
            ]);
    }

    public function test_authenticated_user_can_create_a_payment(): void
    {
        $this->loginAsAdmin();

        $paymentData = [
            'type' => PaymentType::CreditCard,
            'details' => [
                'holder_name' => 'John Doe',
                'number' => '1234567890123456',
                'ccv' => 123,
                'expire_date' => '12/25',
            ],
        ];

        $response = $this->withToken($this->token)->postJson(route('api.v1.payment.create'), $paymentData);

        $response->assertOk()
            ->assertJson(['data' => ['uuid' => $response->json('data.uuid')]
            ]);
    }

    public function test_authenticated_user_can_get_a_payment(): void
    {
        $this->loginAsAdmin();

        $payment = $this->getPayment();

        $response = $this->withToken($this->token)->getJson(route('api.v1.payment.show', ['payment' => $payment->uuid]));

        $response->assertOk()
            ->assertJson([
                'data' => ['uuid' => $payment->uuid]
            ]);
    }

    public function test_authenticated_user_can_update_a_payment(): void
    {
        $this->loginAsAdmin();

        $payment = $this->getPayment();

        $updatedData = [
            'type' => PaymentType::CashOnDelivery,
            'details' => [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'address' => '123 Street',
            ],
        ];

        $response = $this->withToken($this->token)->putJson(route('api.v1.payment.edit', ['payment' => $payment->uuid]), $updatedData);

        $response->assertOk()
            ->assertJson([
                'data' => ['type' => PaymentType::CashOnDelivery->value]
            ]);
    }

    public function test_authenticated_user_can_delete_a_payment(): void
    {
        $this->loginAsAdmin();

        $payment = $this->getPayment();

        $response = $this->withToken($this->token)->deleteJson(route('api.v1.payment.delete', ['payment' => $payment->uuid]));

        $response->assertOk()
            ->assertJson([]);
        $this->assertDatabaseMissing('payments', ['uuid' => $payment->uuid]);
    }

    public function test_unauthenticated_user_cannot_access_protected_methods(): void
    {
        $payment = $this->getPayment();

        // Testing list
        $response = $this->getJson(route('api.v1.payment'));
        $response->assertUnauthorized();

        // Testing store
        $response = $this->postJson(route('api.v1.payment.create'));
        $response->assertUnauthorized();

        // Testing get
        $response = $this->getJson(route('api.v1.payment.edit', $payment));
        $response->assertUnauthorized();

        // Testing update
        $response = $this->putJson(route('api.v1.payment.edit', $payment));
        $response->assertUnauthorized();

        // Testing destroy
        $response = $this->deleteJson(route('api.v1.payment.delete', $payment));
        $response->assertUnauthorized();
    }

    private function loginAsAdmin(): void
    {
        $admin = \App\Models\User::factory([
            'is_admin' => true
        ])->create();
        $this->token = $admin->createToken('test-admin-auth');
        return;
    }

    private function getPayment(): Payment
    {
        return Payment::factory()->create();
    }
}
