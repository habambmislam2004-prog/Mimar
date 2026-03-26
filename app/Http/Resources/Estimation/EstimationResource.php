<?php

namespace App\Http\Resources\Estimation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'estimation_type_id' => $this->estimation_type_id,
            'input_payload' => $this->input_payload,
            'subtotal_cost' => $this->subtotal_cost,
            'waste_cost' => $this->waste_cost,
            'total_cost' => $this->total_cost,
            'estimated_duration_days' => $this->estimated_duration_days,
            'notes' => $this->notes,
            'created_at' => $this->created_at,

            'city' => $this->whenLoaded('city', fn () => [
                'id' => $this->city?->id,
                'name_ar' => $this->city?->name_ar,
                'name_en' => $this->city?->name_en,
            ]),

            'estimation_type' => $this->whenLoaded('estimationType', fn () => [
                'id' => $this->estimationType?->id,
                'code' => $this->estimationType?->code,
                'name_ar' => $this->estimationType?->name_ar,
                'name_en' => $this->estimationType?->name_en,
            ]),

            'items' => EstimationItemResource::collection(
                $this->whenLoaded('items')
            ),

            'matches' => EstimationMatchResource::collection(
                $this->whenLoaded('matches')
            ),
        ];
    }
}