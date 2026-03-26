<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, int $conversationId) {
    $conversation = Conversation::query()->find($conversationId);

    if (! $conversation) {
        return false;
    }

    return $conversation->user_one_id === $user->id
        || $conversation->user_two_id === $user->id;
});