<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(
                $this->faker->numberBetween(1, 3), 
                true
            ),
            'description' => $this->faker->paragraphs(
                $this->faker->numberBetween(1, 3),
                true
            ),
        ];
    }
}
