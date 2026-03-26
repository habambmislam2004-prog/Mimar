<?php

namespace App\Http\Resources\Favorite;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'created_at' => $this->created_at,

            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service?->id,
                'name_ar' => $this->service?->name_ar,
                'name_en' => $this->service?->name_en,
                'price' => $this->service?->price,
                'status' => $this->service?->status,
            ]),
        ];
    }
}