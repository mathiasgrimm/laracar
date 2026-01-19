<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelVersion extends Model
{
    /** @use HasFactory<\Database\Factories\ModelVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'car_model_id',
        'fipe_code',
        'name',
        'slug',
    ];

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
