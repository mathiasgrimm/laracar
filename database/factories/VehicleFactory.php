<?php

namespace Database\Factories;

use App\Enums\Color;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Enums\VehicleStatus;
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
            'color' => fake()->randomElement(Color::cases()),
            'fuel_type' => fake()->randomElement(FuelType::cases()),
            'transmission' => fake()->randomElement(Transmission::cases()),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(VehicleStatus::cases()),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::Ativo,
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::Vendido,
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VehicleStatus::Pausado,
        ]);
    }
}
