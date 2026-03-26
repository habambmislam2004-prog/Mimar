<?php

namespace App\Http\Resources\Estimation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimationItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'material_type_id' => $this->material_type_id,
            'material_name_ar' => $this->materialType?->name_ar,
            'material_name_en' => $this->materialType?->name_en,
            'calculated_quantity' => $this->calculated_quantity,
            'unit' => $this->unit,
            'unit_price' => $this->unit_price,
            'waste_percentage' => $this->waste_percentage,
            'waste_quantity' => $this->waste_quantity,
            'final_quantity' => $this->final_quantity,
            'line_total' => $this->line_total,
        ];
    }
}