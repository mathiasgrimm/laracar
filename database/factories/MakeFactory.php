<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Make>
 */
class MakeFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'fipe_code' => (string) fake()->unique()->numberBetween(1, 999),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
