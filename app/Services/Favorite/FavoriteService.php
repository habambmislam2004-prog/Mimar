<?php

namespace App\Services\Favorite;

use App\Models\User;
use App\Models\Service;
use App\Models\Favorite;
use Illuminate\Support\Collection;

class FavoriteService
{
    public function list(User $user): Collection
    {
        return Favorite::query()
            ->with('service')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function add(User $user, Service $service): Favorite
    {
        return Favorite::query()->firstOrCreate([
            'user_id' => $user->id,
            'service_id' => $service->id,
        ]);
    }

    public function remove(User $user, Service $service): void
    {
        Favorite::query()
            ->where('user_id', $user->id)
            ->where('service_id', $service->id)
            ->delete();
    }
}