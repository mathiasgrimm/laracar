<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Make extends Model
{
    /** @use HasFactory<\Database\Factories\MakeFactory> */
    use HasFactory;

    protected $fillable = [
        'fipe_code',
        'name',
        'slug',
    ];

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }
}
