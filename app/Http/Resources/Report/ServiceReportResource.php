<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'reason' => $this->reason,
            'status' => $this->status,
            'reviewed_at' => $this->reviewed_at,
            'created_at' => $this->created_at,

            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service?->id,
                'name_ar' => $this->service?->name_ar,
                'name_en' => $this->service?->name_en,
            ]),

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ]),
        ];
    }
}