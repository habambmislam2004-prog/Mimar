<?php

namespace Database\Seeders;

use App\Enums\SystemRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            'view-cities',
            'create-cities',
            'edit-cities',
            'delete-cities',

            'view-business-activity-types',
            'create-business-activity-types',
            'edit-business-activity-types',
            'delete-business-activity-types',

            'view-business-accounts',
            'approve-business-accounts',
            'reject-business-accounts',
            'delete-business-accounts',

            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            'view-subcategories',
            'create-subcategories',
            'edit-subcategories',
            'delete-subcategories',

            'view-dynamic-fields',
            'create-dynamic-fields',
            'edit-dynamic-fields',
            'delete-dynamic-fields',

            'view-services',
            'approve-services',
            'reject-services',
            'delete-services',

            'view-orders',
            'delete-orders',

            'view-ratings',
            'delete-ratings',

            'view-reports',
            'resolve-reports',
            'delete-reports',

            'view-sliders',
            'create-sliders',
            'edit-sliders',
            'delete-sliders',

            'view-notifications',
            'send-notifications',

            'view-estimation-types',
            'create-estimation-types',
            'edit-estimation-types',
            'delete-estimation-types',

            'view-material-types',
            'create-material-types',
            'edit-material-types',
            'delete-material-types',

            'view-city-material-prices',
            'create-city-material-prices',
            'edit-city-material-prices',
            'delete-city-material-prices',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => SystemRole::SUPER_ADMIN->value,
            'guard_name' => 'web',
        ]);

        $adminRole = Role::query()->firstOrCreate([
            'name' => SystemRole::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $userRole = Role::query()->firstOrCreate([
            'name' => SystemRole::USER->value,
            'guard_name' => 'web',
        ]);

        $superAdminRole->syncPermissions(Permission::all());

        $adminRole->syncPermissions([
            'view-users',
            'view-cities',
            'create-cities',
            'edit-cities',
            'view-business-accounts',
            'approve-business-accounts',
            'reject-business-accounts',
            'view-categories',
            'create-categories',
            'edit-categories',
            'view-subcategories',
            'create-subcategories',
            'edit-subcategories',
            'view-dynamic-fields',
            'create-dynamic-fields',
            'edit-dynamic-fields',
            'view-services',
            'approve-services',
            'reject-services',
            'view-orders',
            'view-ratings',
            'view-reports',
            'resolve-reports',
            'view-sliders',
            'create-sliders',
            'edit-sliders',
            'view-notifications',
            'send-notifications',
            'view-estimation-types',
            'create-estimation-types',
            'edit-estimation-types',
            'view-material-types',
            'create-material-types',
            'edit-material-types',
            'view-city-material-prices',
            'create-city-material-prices',
            'edit-city-material-prices',
        ]);

        $userRole->syncPermissions([]);
    }
}