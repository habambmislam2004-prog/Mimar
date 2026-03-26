<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'user_one_id' => $this->user_one_id,
            'user_two_id' => $this->user_two_id,
            'last_message_id' => $this->last_message_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service?->id,
                'name_ar' => $this->service?->name_ar,
                'name_en' => $this->service?->name_en,
            ]),

            'last_message' => $this->whenLoaded('lastMessage', fn () => [
                'id' => $this->lastMessage?->id,
                'body' => $this->lastMessage?->body,
                'sender_id' => $this->lastMessage?->sender_id,
                'created_at' => $this->lastMessage?->created_at,
            ]),
        ];
    }
}