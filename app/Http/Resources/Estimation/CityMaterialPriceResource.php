<?php

namespace App\Http\Resources\Estimation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityMaterialPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city_id' => $this->city_id,
            'material_type_id' => $this->material_type_id,
            'price' => $this->price,
            'currency' => $this->currency,
            'effective_from' => $this->effective_from,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at,

            'city' => $this->whenLoaded('city', fn () => [
                'id' => $this->city?->id,
                'name_ar' => $this->city?->name_ar,
                'name_en' => $this->city?->name_en,
            ]),

            'material_type' => $this->whenLoaded('materialType', fn () => [
                'id' => $this->materialType?->id,
                'code' => $this->materialType?->code,
                'name_ar' => $this->materialType?->name_ar,
                'name_en' => $this->materialType?->name_en,
                'base_unit' => $this->materialType?->base_unit,
            ]),
        ];
    }
}