<?php

namespace App\Services\Auth;

use App\Enums\SystemRole;
use App\Exceptions\DomainException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => $data['password'],
            'locale' => $data['locale'] ?? config('app.locale'),
            'is_active' => true,
        ]);
        $user->assignRole(SystemRole::USER->value);
        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
        $user = User::query()
            ->where('email', $data['login'])
            ->orWhere('phone', $data['login'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new DomainException(__('messages.invalid_credentials'));
        }

        if (! $user->is_active) {
            throw new DomainException(__('messages.forbidden'));
        }

        $user->update([
            'last_login_at' => now(),
        ]);

        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}