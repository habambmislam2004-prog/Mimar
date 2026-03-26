<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_status_changed',
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'title' => __('messages.order_status_changed'),
            'message' => match ($this->order->status) {
            'pending' => __('messages.new_order_created'),
            'accepted' => __('messages.order_accepted'),
            'rejected' => __('messages.order_rejected'),
            'cancelled' => __('messages.order_cancelled'),
                default => __('messages.success'),
            },
        ];
    }
}