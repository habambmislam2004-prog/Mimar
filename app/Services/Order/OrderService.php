<?php

namespace App\Services\Order;

use App\Models\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\BusinessAccount;
use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Collection;

class OrderService
{
    public function listSentOrders(User $user): Collection
    {
        return Order::query()
            ->with(['service', 'senderBusinessAccount', 'receiverBusinessAccount', 'rating'])
            ->whereHas('senderBusinessAccount', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
    }

    public function listReceivedOrders(User $user): Collection
    {
        return Order::query()
           ->with(['service', 'senderBusinessAccount', 'receiverBusinessAccount', 'rating'])
            ->whereHas('receiverBusinessAccount', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
    }

    public function create(User $user, Service $service, array $data): Order
    {
        $senderBusinessAccount = BusinessAccount::query()->findOrFail($data['sender_business_account_id']);

        $this->ensureBusinessAccountOwnership($user, $senderBusinessAccount);

        if (! $senderBusinessAccount->isApproved()) {
            throw new DomainException(__('messages.business_account_not_approved'));
        }

        if ($service->status !== 'approved') {
            throw new DomainException(__('messages.service_not_available_for_order'));
        }

        if ($service->business_account_id === $senderBusinessAccount->id) {
            throw new DomainException(__('messages.cannot_order_own_service'));
        }

        return Order::query()->create([
            'service_id' => $service->id,
            'sender_business_account_id' => $senderBusinessAccount->id,
            'receiver_business_account_id' => $service->business_account_id,
            'quantity' => $data['quantity'],
            'details' => $data['details'] ?? null,
            'needed_at' => $data['needed_at'] ?? null,
            'status' => OrderStatus::PENDING->value,
        ])->load(['service', 'senderBusinessAccount', 'receiverBusinessAccount']);
        $order = Order::query()->create([
    'service_id' => $service->id,
    'sender_business_account_id' => $senderBusinessAccount->id,
    'receiver_business_account_id' => $service->business_account_id,
    'quantity' => $data['quantity'],
    'details' => $data['details'] ?? null,
    'needed_at' => $data['needed_at'] ?? null,
    'status' => OrderStatus::PENDING->value,
])->load(['service', 'senderBusinessAccount', 'receiverBusinessAccount']);

  $order->receiverBusinessAccount?->user?->notify(
    new OrderStatusChangedNotification($order)
);

return $order;
    }

    public function accept(User $user, Order $order): Order
    {
        $this->ensureReceiverOwnership($user, $order);
        $this->ensurePending($order);

        $order->update([
            'status' => OrderStatus::ACCEPTED->value,
            'accepted_at' => now(),
            'rejected_at' => null,
            'cancelled_at' => null,
        ]);

        return $order->refresh()->load(['service', 'senderBusinessAccount', 'receiverBusinessAccount']);
    $order->senderBusinessAccount?->user?->notify(
    new OrderStatusChangedNotification($order));
    }

    public function reject(User $user, Order $order): Order
    {
        $this->ensureReceiverOwnership($user, $order);
        $this->ensurePending($order);

        $order->update([
            'status' => OrderStatus::REJECTED->value,
            'accepted_at' => null,
            'rejected_at' => now(),
            'cancelled_at' => null,
        ]);

        return $order->refresh()->load(['service', 'senderBusinessAccount', 'receiverBusinessAccount']);
   $order->senderBusinessAccount?->user?->notify(
    new OrderStatusChangedNotification($order));
     }

    public function cancel(User $user, Order $order): Order
    {
        $this->ensureSenderOwnership($user, $order);

        if ($order->status !== OrderStatus::PENDING->value) {
            throw new DomainException(__('messages.order_cannot_be_cancelled'));
        }

        $order->update([
            'status' => OrderStatus::CANCELLED->value,
            'accepted_at' => null,
            'rejected_at' => null,
            'cancelled_at' => now(),
        ]);

        return $order->refresh()->load(['service', 'senderBusinessAccount', 'receiverBusinessAccount']);
    $order->receiverBusinessAccount?->user?->notify(
    new OrderStatusChangedNotification($order));
     }

    protected function ensureBusinessAccountOwnership(User $user, BusinessAccount $businessAccount): void
    {
        if ($businessAccount->user_id !== $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function ensureReceiverOwnership(User $user, Order $order): void
    {
        if ($order->receiverBusinessAccount?->user_id !== $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function ensureSenderOwnership(User $user, Order $order): void
    {
        if ($order->senderBusinessAccount?->user_id !== $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function ensurePending(Order $order): void
    {
        if ($order->status !== OrderStatus::PENDING->value) {
            throw new DomainException(__('messages.order_is_not_pending'));
        }
    }
}