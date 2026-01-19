<?php

namespace Database\Factories;

use App\Models\ModelVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $yearManufacture = fake()->numberBetween(2010, 2025);

        return [
            'model_version_id' => ModelVersion::factory(),
            'year_manufacture' => $yearManufacture,
            'year_model' => fake()->randomElement([$yearManufacture, $yearManufacture + 1]),
            'price' => fake()->randomFloat(2, 15000, 500000),
            'mileage' => fake()->numberBetween(0, 200000),
            'color' => fake()->randomElement(['Preto', 'Branco', 'Prata', 'Cinza', 'Vermelho', 'Azul']),
            'fuel_type' => fake()->randomElement(['gasolina', 'etanol', 'flex', 'diesel', 'eletrico', 'hibrido']),
            'transmission' => fake()->randomElement(['manual', 'automatico', 'cvt', 'automatizado']),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['ativo', 'vendido', 'pausado']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ativo',
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'vendido',
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pausado',
        ]);
    }
}
