<?php

namespace App\Services\Estimation;

use App\Models\User;
use App\Models\Estimation;
use App\Exceptions\DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EstimationHistoryService
{
    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Estimation::query()
            ->with([
                'city',
                'estimationType',
                'items.materialType',
                'matches.service',
                'matches.businessAccount',
            ])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function showForUser(User $user, Estimation $estimation): Estimation
    {
        if ($estimation->user_id !== $user->id) {
            throw new DomainException(__('messages.forbidden'));
        }

        return $estimation->load([
            'city',
            'estimationType',
            'items.materialType',
            'matches.service',
            'matches.businessAccount',
        ]);
    }
}