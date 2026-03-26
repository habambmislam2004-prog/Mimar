<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'base_unit',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function cityPrices(): HasMany
    {
        return $this->hasMany(CityMaterialPrice::class);
    }

    public function estimationItems(): HasMany
    {
        return $this->hasMany(EstimationItem::class);
    }
}