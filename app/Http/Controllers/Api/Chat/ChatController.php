<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Chat\ChatService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Chat\StoreMessageRequest;
use App\Http\Resources\Chat\MessageResource;
use App\Http\Resources\Chat\ConversationResource;
use App\Http\Requests\Chat\StoreConversationRequest;

class ChatController extends ApiController
{
    public function __construct(
        protected ChatService $service
    ) {
    }

    public function conversations(Request $request): JsonResponse
    {
        $conversations = $this->service->listConversations($request->user());

        return $this->successResponse(
            ConversationResource::collection($conversations),
            __('messages.success')
        );
    }

    public function createConversation(StoreConversationRequest $request): JsonResponse
    {
        $conversation = $this->service->createOrGetConversation(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            new ConversationResource($conversation),
            __('messages.created_successfully'),
            201
        );
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $messages = $this->service->getConversationMessages(
            $request->user(),
            $conversation
        );

        return $this->successResponse(
            MessageResource::collection($messages),
            __('messages.success')
        );
    }

    public function sendMessage(
        StoreMessageRequest $request,
        Conversation $conversation
    ): JsonResponse {
        $message = $this->service->sendMessage(
            $request->user(),
            $conversation,
            $request->validated()
        );

        return $this->successResponse(
            new MessageResource($message),
            __('messages.created_successfully'),
            201
        );
    }

    public function markAsRead(Request $request, Conversation $conversation): JsonResponse
    {
        $this->service->markConversationAsRead(
            $request->user(),
            $conversation
        );

        return $this->successResponse(
            null,
            __('messages.updated_successfully')
        );
    }
}