<?php

namespace Database\Factories;

use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModelVersion>
 */
class ModelVersionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true).' '.fake()->randomFloat(1, 1, 3);

        return [
            'car_model_id' => CarModel::factory(),
            'fipe_code' => fake()->unique()->numberBetween(1000, 99999),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
