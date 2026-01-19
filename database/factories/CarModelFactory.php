<?php

namespace Database\Factories;

use App\Models\Make;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarModel>
 */
class CarModelFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true).' '.fake()->randomNumber(3);

        return [
            'make_id' => Make::factory(),
            'fipe_code' => fake()->unique()->numberBetween(1000, 99999),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
