<?php

use App\Enums\Color;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Enums\VehicleStatus;
use App\Models\ModelVersion;
use App\Models\Vehicle;

it('can create a vehicle', function () {
    $vehicle = Vehicle::factory()->create();

    expect($vehicle)->toBeInstanceOf(Vehicle::class)
        ->and($vehicle->id)->toBeInt()
        ->and($vehicle->year_manufacture)->toBeInt()
        ->and($vehicle->year_model)->toBeInt()
        ->and($vehicle->price)->toBeString()
        ->and($vehicle->mileage)->toBeInt()
        ->and($vehicle->color)->toBeInstanceOf(Color::class)
        ->and($vehicle->fuel_type)->toBeInstanceOf(FuelType::class)
        ->and($vehicle->transmission)->toBeInstanceOf(Transmission::class)
        ->and($vehicle->status)->toBeInstanceOf(VehicleStatus::class);
});

it('belongs to a model version', function () {
    $version = ModelVersion::factory()->create();
    $vehicle = Vehicle::factory()->create(['model_version_id' => $version->id]);

    expect($vehicle->modelVersion)->toBeInstanceOf(ModelVersion::class)
        ->and($vehicle->modelVersion->id)->toBe($version->id);
});

it('can have active status', function () {
    $vehicle = Vehicle::factory()->active()->create();

    expect($vehicle->status)->toBe(VehicleStatus::Ativo);
});

it('can have sold status', function () {
    $vehicle = Vehicle::factory()->sold()->create();

    expect($vehicle->status)->toBe(VehicleStatus::Vendido);
});

it('can have paused status', function () {
    $vehicle = Vehicle::factory()->paused()->create();

    expect($vehicle->status)->toBe(VehicleStatus::Pausado);
});

it('implements HasMedia interface', function () {
    $vehicle = Vehicle::factory()->create();

    expect($vehicle)->toBeInstanceOf(\Spatie\MediaLibrary\HasMedia::class);
});
