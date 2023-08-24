<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\PaymentType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = \App\Models\Payment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = PaymentType::cases()[rand(0, count(PaymentType::cases()) - 1)]->value;
        return match ($type) {
            PaymentType::CreditCard->value  => [
                'type' => $type,
                'details' => [
                    'holder_name' => $this->faker->name,
                    'number' => $this->faker->numberBetween(100000000, 999999999),
                    'ccv' => $this->faker->numberBetween(100, 999),
                    'expire_date' => \Carbon\Carbon::parse($this->faker->date)->format('m/y'),
                ]
            ],
            PaymentType::CashOnDelivery->value => [
                'type' => $type,
                'details' => [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'address' => $this->faker->address,
                ]
            ],
            PaymentType::BankTransfer->value => [
                'type' => $type,
                'details' => [
                    'swift' => $this->faker->swiftBicNumber,
                    'iban' => $this->faker->iban,
                    'name' => $this->faker->name,
                ]
            ],
        };
    }
}
