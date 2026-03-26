<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Models\DeviceToken;

class DeviceTokenService
{
    public function store(User $user, array $data): DeviceToken
    {
        return DeviceToken::query()->updateOrCreate(
            [
                'token' => $data['token'],
            ],
            [
                'user_id' => $user->id,
                'platform' => $data['platform'] ?? null,
            ]
        );
    }

    public function delete(User $user, string $token): void
    {
        DeviceToken::query()
            ->where('user_id', $user->id)
            ->where('token', $token)
            ->delete();
    }
}