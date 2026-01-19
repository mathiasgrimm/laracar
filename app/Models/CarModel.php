<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CarModel extends Model
{
    /** @use HasFactory<\Database\Factories\CarModelFactory> */
    use HasFactory;

    protected $fillable = [
        'make_id',
        'name',
        'slug',
    ];

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ModelVersion::class);
    }

    public function vehicles(): HasManyThrough
    {
        return $this->hasManyThrough(Vehicle::class, ModelVersion::class);
    }
}
