<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImportFile>
 */
class ImportFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_name'  => $this->faker->lexify('file_????.xlsx'),
            'stored_name'    => $this->faker->uuid() . '.xlsx',
        ];
    }
}
