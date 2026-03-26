<?php

namespace App\Services\Rating;

use App\Models\User;
use App\Models\Order;
use App\Models\Rating;
use App\Enums\OrderStatus;
use App\Exceptions\DomainException;
use Illuminate\Support\Collection;

class RatingService
{
    public function listServiceRatings(int $serviceId): Collection
    {
        return Rating::query()
            ->with(['user', 'service'])
            ->where('service_id', $serviceId)
            ->latest()
            ->get();
    }

    public function create(User $user, Order $order, array $data): Rating
    {
        $order->loadMissing(['service', 'senderBusinessAccount', 'rating']);

        $this->ensureSenderOwnership($user, $order);
        $this->ensureAccepted($order);
        $this->ensureNotRated($order);

        return Rating::query()->create([
            'order_id' => $order->id,
            'service_id' => $order->service_id,
            'user_id' => $user->id,
            'score' => $data['score'],
            'comment' => $data['comment'] ?? null,
        ])->load(['user', 'service']);
    }

    protected function ensureSenderOwnership(User $user, Order $order): void
    {
        if ($order->senderBusinessAccount?->user_id !== $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }
    }

    protected function ensureAccepted(Order $order): void
    {
        if ($order->status !== OrderStatus::ACCEPTED->value) {
            throw new DomainException(__('messages.rating_requires_accepted_order'));
        }
    }

    protected function ensureNotRated(Order $order): void
    {
        if ($order->rating()->exists()) {
            throw new DomainException(__('messages.order_already_rated'));
        }
    }
}