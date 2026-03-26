<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstimationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'unit_type',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function estimations(): HasMany
    {
        return $this->hasMany(Estimation::class);
    }
}