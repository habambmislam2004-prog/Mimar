<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as LaravelNotification; // 1. غيرنا الاسم هنا
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification; // 2. أعطينا اسماً مستعاراً هنا

class OrderStatusChangedNotification extends LaravelNotification
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
   public function toFcm($notifiable): CloudMessage
{
    return CloudMessage::new()
        ->withNotification(FirebaseNotification::create(
            'عنوان الإشعار',
            'محتوى الإشعار'  
        ))
        ->withData(['order_id' => (string)$this->order->id]);
}
    protected function getMessageByStatus(): string
    {
        return match ($this->order->status) {
            'pending' => __('messages.new_order_created'),
            'accepted' => __('messages.order_accepted'),
            'rejected' => __('messages.order_rejected'),
            'cancelled' => __('messages.order_cancelled'),
            default => __('messages.success'),
        };
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