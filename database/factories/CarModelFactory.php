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
        $name = fake()->unique()->word();

        return [
            'make_id' => Make::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }
}
