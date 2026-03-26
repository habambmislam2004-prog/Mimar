<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'sender_business_account_id' => $this->sender_business_account_id,
            'receiver_business_account_id' => $this->receiver_business_account_id,
            'quantity' => $this->quantity,
            'details' => $this->details,
            'needed_at' => $this->needed_at,
            'status' => $this->status,
            'accepted_at' => $this->accepted_at,
            'rejected_at' => $this->rejected_at,
            'cancelled_at' => $this->cancelled_at,
            'created_at' => $this->created_at,

            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service?->id,
                'name_ar' => $this->service?->name_ar,
                'name_en' => $this->service?->name_en,
                'price' => $this->service?->price,
            ]),

            'sender_business_account' => $this->whenLoaded('senderBusinessAccount', fn () => [
                'id' => $this->senderBusinessAccount?->id,
                'name_ar' => $this->senderBusinessAccount?->name_ar,
                'name_en' => $this->senderBusinessAccount?->name_en,
            ]),

            'receiver_business_account' => $this->whenLoaded('receiverBusinessAccount', fn () => [
                'id' => $this->receiverBusinessAccount?->id,
                'name_ar' => $this->receiverBusinessAccount?->name_ar,
                'name_en' => $this->receiverBusinessAccount?->name_en,
            ]),
        ];
    }
}