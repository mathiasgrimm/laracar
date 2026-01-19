<?php

namespace App\Models;

use App\Enums\Color;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vehicle extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'model_version_id',
        'year_manufacture',
        'year_model',
        'price',
        'mileage',
        'color',
        'fuel_type',
        'transmission',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'year_manufacture' => 'integer',
            'year_model' => 'integer',
            'mileage' => 'integer',
            'color' => Color::class,
            'fuel_type' => FuelType::class,
            'transmission' => Transmission::class,
            'status' => VehicleStatus::class,
        ];
    }

    public function modelVersion(): BelongsTo
    {
        return $this->belongsTo(ModelVersion::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(300)
            ->height(200)
            ->sharpen(10);

        $this->addMediaConversion('gallery')
            ->width(800)
            ->height(600)
            ->sharpen(10);
    }
}
