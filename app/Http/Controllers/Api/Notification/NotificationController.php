<?php

namespace App\Http\Controllers\Api\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;

class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->get();

        return $this->successResponse(
            $notifications,
            __('messages.success')
        );
    }

    public function unread(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->get();

        return $this->successResponse(
            $notifications,
            __('messages.success')
        );
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->markAsRead();

        return $this->successResponse(
            null,
            __('messages.updated_successfully')
        );
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return $this->successResponse(
            null,
            __('messages.updated_successfully')
        );
    }
}