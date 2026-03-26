<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Service $service
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'service_status_changed',
            'service_id' => $this->service->id,
            'status' => $this->service->status,
            'title' => __('messages.service_status_changed'),
            'message' => match ($this->service->status) {
                'approved' => __('messages.service_approved'),
                'rejected' => __('messages.service_rejected'),
                default => __('messages.success'),
            },
        ];
    }
}