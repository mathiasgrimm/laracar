<?php

use App\Models\ModelVersion;
use App\Models\User;
use App\Models\Vehicle;

it('can create a vehicle', function () {
    $vehicle = Vehicle::factory()->create();

    expect($vehicle)->toBeInstanceOf(Vehicle::class)
        ->and($vehicle->id)->toBeInt()
        ->and($vehicle->year_manufacture)->toBeInt()
        ->and($vehicle->year_model)->toBeInt()
        ->and($vehicle->price)->toBeString()
        ->and($vehicle->mileage)->toBeInt()
        ->and($vehicle->color)->toBeString()
        ->and($vehicle->fuel_type)->toBeIn(['gasolina', 'etanol', 'flex', 'diesel', 'eletrico', 'hibrido'])
        ->and($vehicle->transmission)->toBeIn(['manual', 'automatico', 'cvt', 'automatizado'])
        ->and($vehicle->status)->toBeIn(['ativo', 'vendido', 'pausado']);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

    expect($vehicle->user)->toBeInstanceOf(User::class)
        ->and($vehicle->user->id)->toBe($user->id);
});

it('belongs to a model version', function () {
    $version = ModelVersion::factory()->create();
    $vehicle = Vehicle::factory()->create(['model_version_id' => $version->id]);

    expect($vehicle->modelVersion)->toBeInstanceOf(ModelVersion::class)
        ->and($vehicle->modelVersion->id)->toBe($version->id);
});

it('can have active status', function () {
    $vehicle = Vehicle::factory()->active()->create();

    expect($vehicle->status)->toBe('ativo');
});

it('can have sold status', function () {
    $vehicle = Vehicle::factory()->sold()->create();

    expect($vehicle->status)->toBe('vendido');
});

it('can have paused status', function () {
    $vehicle = Vehicle::factory()->paused()->create();

    expect($vehicle->status)->toBe('pausado');
});

it('deletes vehicles when user is deleted', function () {
    $user = User::factory()->create();
    Vehicle::factory()->count(3)->create(['user_id' => $user->id]);

    expect(Vehicle::where('user_id', $user->id)->count())->toBe(3);

    $user->delete();

    expect(Vehicle::where('user_id', $user->id)->count())->toBe(0);
});

it('deletes vehicles when model version is deleted', function () {
    $version = ModelVersion::factory()->create();
    Vehicle::factory()->count(3)->create(['model_version_id' => $version->id]);

    expect(Vehicle::where('model_version_id', $version->id)->count())->toBe(3);

    $version->delete();

    expect(Vehicle::where('model_version_id', $version->id)->count())->toBe(0);
});

it('implements HasMedia interface', function () {
    $vehicle = Vehicle::factory()->create();

    expect($vehicle)->toBeInstanceOf(\Spatie\MediaLibrary\HasMedia::class);
});
