<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BusinessAccount;

class BusinessAccountPolicy
{
    public function view(User $user, BusinessAccount $businessAccount): bool
    {
        return $businessAccount->user_id === $user->id;
    }

    public function update(User $user, BusinessAccount $businessAccount): bool
    {
        return $businessAccount->user_id === $user->id;
    }

    public function delete(User $user, BusinessAccount $businessAccount): bool
    {
        return $businessAccount->user_id === $user->id;
    }
}