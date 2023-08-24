<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word . '.' . $this->faker->fileExtension;

        return [
            'name' => $name,
            'path' => 'storage/files/' . $name,
            'size' => $this->faker->randomNumber(5),
            'type' => $this->faker->mimeType,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
