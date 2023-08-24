<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['shipped', 'pending payment', 'open', 'paid'];
        /** @var OrderStatus $orderStatus */
        $orderStatus = OrderStatus::factory()->create([
            'title' => $statuses[rand(0, count($statuses) - 1)],
        ]);
        /** @var Payment $payment */
        $payment = Payment::factory()->create();

        /** @var User $user */
        $user = User::factory()->create();

        return [
            'order_status_id' => $orderStatus->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
            'products' => $this->getProducts(),
            'address' => ['shipping' => $this->faker->address, 'billing' => $this->faker->address],
            'delivery_fee' => $this->faker->randomFloat(2, 0, 50),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'shipped_at' => $orderStatus->title === 'shipped' ? now() : null,
        ];
    }

    public function getProducts(): array
    {
        $products = Product::factory(rand(0, 20))->create();
        return $products->map(function ($product) {
            return ['product' => $product->uuid, 'quantity' => $this->faker->randomDigitNotZero()];
        })->all();
    }
}
