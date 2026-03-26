<?php

namespace App\Services\Chat;

use App\Events\MessageSent;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Exceptions\DomainException;
use Illuminate\Support\Collection;

class ChatService
{
    public function listConversations(User $user): Collection
    {
        return Conversation::query()
            ->with(['service', 'lastMessage'])
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->latest('updated_at')
            ->get();
    }

    public function createOrGetConversation(User $user, array $data): Conversation
    {
        $otherUserId = (int) $data['other_user_id'];
        $serviceId = $data['service_id'] ?? null;

        if ($otherUserId === $user->id) {
            throw new DomainException(__('messages.cannot_chat_with_yourself'));
        }

        $userOneId = min($user->id, $otherUserId);
        $userTwoId = max($user->id, $otherUserId);

        $conversation = Conversation::query()
            ->where('user_one_id', $userOneId)
            ->where('user_two_id', $userTwoId)
            ->where('service_id', $serviceId)
            ->first();

        if ($conversation) {
            return $conversation->load(['service', 'lastMessage']);
        }

        return Conversation::query()->create([
            'service_id' => $serviceId,
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
            'last_message_id' => null,
        ])->load(['service', 'lastMessage']);
    }

    public function getConversationMessages(User $user, Conversation $conversation): Collection
    {
        $this->ensureParticipant($user, $conversation);

        return Message::query()
            ->with('sender')
            ->where('conversation_id', $conversation->id)
            ->latest()
            ->get();
    }

   public function sendMessage(User $user, Conversation $conversation, array $data): Message
{
    $this->ensureParticipant($user, $conversation);

    $message = Message::query()->create([
        'conversation_id' => $conversation->id,
        'sender_id' => $user->id,
        'body' => $data['body'],
    ]);

    $conversation->update([
        'last_message_id' => $message->id,
    ]);

    $message->load('sender');

    event(new MessageSent($message));

    return $message;
}
    public function markConversationAsRead(User $user, Conversation $conversation): void
    {
        $this->ensureParticipant($user, $conversation);

        Message::query()
            ->where('conversation_id', $conversation->id)
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update([
                'read_at' => now(),
            ]);
    }

    protected function ensureParticipant(User $user, Conversation $conversation): void
    {
        if (
            $conversation->user_one_id !== $user->id &&
            $conversation->user_two_id !== $user->id
        ) {
            throw new DomainException(__('messages.forbidden'));
        }
    }
}