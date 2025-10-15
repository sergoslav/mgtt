<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Row>
 */
class RowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'    => $this->faker->unique()->randomNumber(8),
            'name'  =>  $this->faker->unique()->word,
            'date'  =>  $this->faker->date(),
        ];
    }
}
