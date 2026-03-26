<?php

namespace App\Http\Resources\Estimation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimationMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'business_account_id' => $this->business_account_id,
            'match_type' => $this->match_type,
            'score' => $this->score,

            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service?->id,
                'name_ar' => $this->service?->name_ar,
                'name_en' => $this->service?->name_en,
                'price' => $this->service?->price,
                'status' => $this->service?->status,
            ]),

            'business_account' => $this->whenLoaded('businessAccount', fn () => [
                'id' => $this->businessAccount?->id,
                'name_ar' => $this->businessAccount?->name_ar,
                'name_en' => $this->businessAccount?->name_en,
            ]),
        ];
    }
}