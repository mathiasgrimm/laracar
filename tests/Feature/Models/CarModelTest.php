<?php

use App\Models\CarModel;
use App\Models\Make;
use App\Models\Vehicle;

it('can create a car model', function () {
    $carModel = CarModel::factory()->create();

    expect($carModel)->toBeInstanceOf(CarModel::class)
        ->and($carModel->id)->toBeInt()
        ->and($carModel->fipe_code)->toBeInt()
        ->and($carModel->name)->toBeString()
        ->and($carModel->slug)->toBeString();
});

it('belongs to a make', function () {
    $make = Make::factory()->create();
    $carModel = CarModel::factory()->create(['make_id' => $make->id]);

    expect($carModel->make)->toBeInstanceOf(Make::class)
        ->and($carModel->make->id)->toBe($make->id);
});

it('has many vehicles', function () {
    $carModel = CarModel::factory()->create();
    Vehicle::factory()->count(3)->create(['car_model_id' => $carModel->id]);

    expect($carModel->vehicles)->toHaveCount(3)
        ->and($carModel->vehicles->first())->toBeInstanceOf(Vehicle::class);
});

it('has unique fipe_code per make', function () {
    $make = Make::factory()->create();
    CarModel::factory()->create(['make_id' => $make->id, 'fipe_code' => 1234]);

    expect(fn () => CarModel::factory()->create(['make_id' => $make->id, 'fipe_code' => 1234]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('allows same fipe_code for different makes', function () {
    $make1 = Make::factory()->create();
    $make2 = Make::factory()->create();

    $carModel1 = CarModel::factory()->create(['make_id' => $make1->id, 'fipe_code' => 1234]);
    $carModel2 = CarModel::factory()->create(['make_id' => $make2->id, 'fipe_code' => 1234]);

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
