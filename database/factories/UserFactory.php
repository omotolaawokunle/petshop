<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'email_verified_at' => now(),
            'password' => '$2y$10$hXwPIp6/Z7u35f3X5u7vveGC6D8m1DKTfqhXyE2okjq4MlYfcIA9y',
            'avatar' => null,
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'is_marketing' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function isAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'email' => 'admin@buckhill.co.uk',
            'password' => '$2y$10$LVobo5fQdtRv0RM01Rri7eh91KMjoPbLZD6roICVsrFKG/js0Bwo.'
        ]);
    }
}
