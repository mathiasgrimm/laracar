<?php

use App\Models\CarModel;
use App\Models\Make;

it('can create a make', function () {
    $make = Make::factory()->create();

    expect($make)->toBeInstanceOf(Make::class)
        ->and($make->id)->toBeInt()
        ->and($make->fipe_code)->toBeString()
        ->and($make->name)->toBeString()
        ->and($make->slug)->toBeString();
});

it('has many car models', function () {
    $make = Make::factory()->create();
    $carModels = CarModel::factory()->count(3)->create(['make_id' => $make->id]);

    expect($make->carModels)->toHaveCount(3)
        ->and($make->carModels->first())->toBeInstanceOf(CarModel::class);
});

it('has unique fipe_code', function () {
    $make = Make::factory()->create(['fipe_code' => '123']);

    expect(fn () => Make::factory()->create(['fipe_code' => '123']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('has unique slug', function () {
    $make = Make::factory()->create(['slug' => 'test-slug']);

    expect(fn () => Make::factory()->create(['slug' => 'test-slug']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
