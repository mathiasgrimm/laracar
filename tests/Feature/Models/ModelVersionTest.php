<?php

use App\Models\CarModel;
use App\Models\ModelVersion;
use App\Models\Vehicle;

it('can create a model version', function () {
    $version = ModelVersion::factory()->create();

    expect($version)->toBeInstanceOf(ModelVersion::class)
        ->and($version->id)->toBeInt()
        ->and($version->fipe_code)->toBeInt()
        ->and($version->name)->toBeString()
        ->and($version->slug)->toBeString();
});

it('belongs to a car model', function () {
    $carModel = CarModel::factory()->create();
    $version = ModelVersion::factory()->create(['car_model_id' => $carModel->id]);

    expect($version->carModel)->toBeInstanceOf(CarModel::class)
        ->and($version->carModel->id)->toBe($carModel->id);
});

it('has many vehicles', function () {
    $version = ModelVersion::factory()->create();
    Vehicle::factory()->count(3)->create(['model_version_id' => $version->id]);

    expect($version->vehicles)->toHaveCount(3)
        ->and($version->vehicles->first())->toBeInstanceOf(Vehicle::class);
});

it('has unique fipe_code per car model', function () {
    $carModel = CarModel::factory()->create();
    ModelVersion::factory()->create(['car_model_id' => $carModel->id, 'fipe_code' => 1234]);

    expect(fn () => ModelVersion::factory()->create(['car_model_id' => $carModel->id, 'fipe_code' => 1234]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('allows same fipe_code for different car models', function () {
    $carModel1 = CarModel::factory()->create();
    $carModel2 = CarModel::factory()->create();

    $version1 = ModelVersion::factory()->create(['car_model_id' => $carModel1->id, 'fipe_code' => 1234]);
    $version2 = ModelVersion::factory()->create(['car_model_id' => $carModel2->id, 'fipe_code' => 1234]);

    expect($version1)->toBeInstanceOf(ModelVersion::class)
        ->and($version2)->toBeInstanceOf(ModelVersion::class);
});
