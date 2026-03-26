<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'estimation_type_id',
        'input_payload',
        'subtotal_cost',
        'waste_cost',
        'total_cost',
        'estimated_duration_days',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'input_payload' => 'array',
            'subtotal_cost' => 'decimal:2',
            'waste_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function estimationType(): BelongsTo
    {
        return $this->belongsTo(EstimationType::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimationItem::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EstimationServiceMatch::class);
    }
}