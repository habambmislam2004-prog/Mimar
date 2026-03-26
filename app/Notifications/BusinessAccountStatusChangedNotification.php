<?php

namespace App\Notifications;

use App\Models\BusinessAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BusinessAccountStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected BusinessAccount $businessAccount
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'business_account_status_changed',
            'business_account_id' => $this->businessAccount->id,
            'status' => $this->businessAccount->status,
            'title' => __('messages.business_account_status_changed'),
            'message' => $this->buildMessage(),
        ];
    }

    protected function buildMessage(): string
    {
        return match ($this->businessAccount->status) {
            'approved' => __('messages.business_account_approved'),
            'rejected' => __('messages.business_account_rejected'),
            default => __('messages.success'),
        };
    }
}