<?php

namespace Database\Seeders;

use App\Enums\SystemRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            [
                'email' => 'admin@realestate.com',
            ],
            [
                'name' => 'Super Admin',
                'phone' => '09999999999',
                'password' => '12345678',
                'locale' => 'ar',
                'is_active' => true,
            ]
        );

        if (! $admin->hasRole(SystemRole::SUPER_ADMIN->value)) {
            $admin->assignRole(SystemRole::SUPER_ADMIN->value);
        }
    }
}