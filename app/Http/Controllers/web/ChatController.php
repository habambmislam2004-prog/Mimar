<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Service;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreMessageRequest;
use App\Http\Requests\Chat\StoreConversationRequest;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $authUser = $request->user();

        $conversations = Conversation::query()
            ->with([
                'service',
                'userOne',
                'userTwo',
                'lastMessage',
                'messages.sender',
            ])
            ->where(function ($query) use ($authUser) {
                $query->where('user_one_id', $authUser->id)
                    ->orWhere('user_two_id', $authUser->id);
            })
            ->latest()
            ->get();

        $selectedConversation = null;

        if ($request->filled('conversation')) {
            $selectedConversation = $conversations->firstWhere('id', (int) $request->query('conversation'));
        }

        if (! $selectedConversation && $request->filled('service')) {
            $serviceId = (int) $request->query('service');

            $selectedConversation = $conversations->first(function ($conversation) use ($serviceId) {
                return (int) $conversation->service_id === $serviceId;
            });
        }

        if (! $selectedConversation) {
            $selectedConversation = $conversations->first();
        }

        $otherUsers = User::query()
            ->where('id', '!=', $authUser->id)
            ->latest()
            ->take(20)
            ->get();

        return view('public.chat.index', compact(
            'conversations',
            'selectedConversation',
            'otherUsers'
        ));
    }

    public function storeConversation(StoreConversationRequest $request): RedirectResponse
    {
        $authUser = $request->user();
        $otherUserId = (int) $request->validated()['other_user_id'];
        $serviceId = $request->validated()['service_id'] ?? null;

        $existingConversation = Conversation::query()
            ->where(function ($query) use ($authUser, $otherUserId) {
                $query->where('user_one_id', $authUser->id)
                    ->where('user_two_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($authUser, $otherUserId) {
                $query->where('user_one_id', $otherUserId)
                    ->where('user_two_id', $authUser->id);
            })
            ->when($serviceId, function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->first();

        if ($existingConversation) {
            return redirect()->route('chat.index', [
                'conversation' => $existingConversation->id,
            ]);
        }

        $conversation = Conversation::query()->create([
            'service_id' => $serviceId,
            'user_one_id' => $authUser->id,
            'user_two_id' => $otherUserId,
            'last_message_id' => null,
        ]);

        return redirect()->route('chat.index', [
            'conversation' => $conversation->id,
        ]);
    }

    public function startFromService(Request $request, Service $service): RedirectResponse
    {
        $authUser = $request->user();
        $providerUserId = $service->businessAccount?->user_id;

        if (! $providerUserId || (int) $providerUserId === (int) $authUser->id) {
            return back()->with('error', __('messages.forbidden'));
        }

        $existingConversation = Conversation::query()
            ->where('service_id', $service->id)
            ->where(function ($query) use ($authUser, $providerUserId) {
                $query->where(function ($q) use ($authUser, $providerUserId) {
                    $q->where('user_one_id', $authUser->id)
                        ->where('user_two_id', $providerUserId);
                })->orWhere(function ($q) use ($authUser, $providerUserId) {
                    $q->where('user_one_id', $providerUserId)
                        ->where('user_two_id', $authUser->id);
                });
            })
            ->first();

        if ($existingConversation) {
            return redirect()->route('chat.index', [
                'conversation' => $existingConversation->id,
            ]);
        }

        $conversation = Conversation::query()->create([
            'service_id' => $service->id,
            'user_one_id' => $authUser->id,
            'user_two_id' => $providerUserId,
            'last_message_id' => null,
        ]);

        return redirect()->route('chat.index', [
            'conversation' => $conversation->id,
        ]);
    }

    public function storeMessage(StoreMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $authUser = $request->user();

        abort_unless(
            $conversation->user_one_id === $authUser->id || $conversation->user_two_id === $authUser->id,
            403
        );

        $message = Message::query()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $authUser->id,
            'body' => $request->validated()['body'],
        ]);

        $conversation->update([
            'last_message_id' => $message->id,
        ]);

        return redirect()->route('chat.index', [
            'conversation' => $conversation->id,
        ]);
    }
}