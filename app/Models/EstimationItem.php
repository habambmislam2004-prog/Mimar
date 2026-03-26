<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstimationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimation_id',
        'material_type_id',
        'calculated_quantity',
        'unit',
        'unit_price',
        'waste_percentage',
        'waste_quantity',
        'final_quantity',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'calculated_quantity' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'waste_percentage' => 'decimal:2',
            'waste_quantity' => 'decimal:3',
            'final_quantity' => 'decimal:3',
            'line_total' => 'decimal:2',
        ];
    }

    public function estimation(): BelongsTo
    {
        return $this->belongsTo(Estimation::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }
}