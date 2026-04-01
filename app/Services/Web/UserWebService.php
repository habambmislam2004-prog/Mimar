<?php

namespace App\Services\Web;

use App\Models\User;
use App\Enums\SystemRole;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserWebService
{
    public function paginate(int $perPage = 12): LengthAwarePaginator
    {
        return User::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'locale' => $data['locale'] ?? 'ar',
            'is_active' => (bool) $data['is_active'],
            'account_type' => $data['account_type'],
        ]);

        $user->syncRoles([$data['account_type']]);

        return $user->refresh();
    }

    public function update(User $user, array $data): User
    {
        if ($user->hasRole(SystemRole::SUPER_ADMIN->value)) {
            throw ValidationException::withMessages([
                'account_type' => __('messages.not_allowed_action'),
            ]);
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'locale' => $data['locale'] ?? $user->locale,
            'is_active' => (bool) $data['is_active'],
            'account_type' => $data['account_type'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = bcrypt($data['password']);
        }

        $user->update($payload);
        $user->syncRoles([$data['account_type']]);

        return $user->refresh();
    }

    public function delete(User $user, ?User $authUser = null): void
    {
        if ($user->hasRole(SystemRole::SUPER_ADMIN->value)) {
            throw ValidationException::withMessages([
                'user' => __('messages.not_allowed_action'),
            ]);
        }

        if ($authUser && $authUser->id === $user->id) {
            throw ValidationException::withMessages([
                'user' => __('messages.not_allowed_action'),
            ]);
        }

        $user->delete();
    }
}