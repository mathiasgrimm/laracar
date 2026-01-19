<?php

use App\Models\CarModel;
use App\Models\Make;
use App\Models\ModelVersion;
use App\Models\Vehicle;

it('can create a car model', function () {
    $carModel = CarModel::factory()->create();

    expect($carModel)->toBeInstanceOf(CarModel::class)
        ->and($carModel->id)->toBeInt()
        ->and($carModel->name)->toBeString()
        ->and($carModel->slug)->toBeString();
});

it('belongs to a make', function () {
    $make = Make::factory()->create();
    $carModel = CarModel::factory()->create(['make_id' => $make->id]);

    expect($carModel->make)->toBeInstanceOf(Make::class)
        ->and($carModel->make->id)->toBe($make->id);
});

it('has many versions', function () {
    $carModel = CarModel::factory()->create();
    ModelVersion::factory()->count(3)->create(['car_model_id' => $carModel->id]);

    expect($carModel->versions)->toHaveCount(3)
        ->and($carModel->versions->first())->toBeInstanceOf(ModelVersion::class);
});

it('has many vehicles through versions', function () {
    $carModel = CarModel::factory()->create();
    $version = ModelVersion::factory()->create(['car_model_id' => $carModel->id]);
    Vehicle::factory()->count(3)->create(['model_version_id' => $version->id]);

    expect($carModel->vehicles)->toHaveCount(3)
        ->and($carModel->vehicles->first())->toBeInstanceOf(Vehicle::class);
});

it('has unique slug per make', function () {
    $make = Make::factory()->create();
    CarModel::factory()->create(['make_id' => $make->id, 'slug' => 'civic']);

    expect(fn () => CarModel::factory()->create(['make_id' => $make->id, 'slug' => 'civic']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('allows same slug for different makes', function () {
    $make1 = Make::factory()->create();
    $make2 = Make::factory()->create();

    $carModel1 = CarModel::factory()->create(['make_id' => $make1->id, 'slug' => 'civic']);
    $carModel2 = CarModel::factory()->create(['make_id' => $make2->id, 'slug' => 'civic']);

    expect($carModel1)->toBeInstanceOf(CarModel::class)
        ->and($carModel2)->toBeInstanceOf(CarModel::class);
});

it('deletes car models when make is deleted', function () {
    $make = Make::factory()->create();
    CarModel::factory()->count(3)->create(['make_id' => $make->id]);

    expect(CarModel::where('make_id', $make->id)->count())->toBe(3);

    $make->delete();

    expect(CarModel::where('make_id', $make->id)->count())->toBe(0);
});
