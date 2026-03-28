<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\Web\RoleWebService;
use App\Http\Requests\Web\Role\StoreRoleWebRequest;
use App\Http\Requests\Web\Role\UpdateRoleWebRequest;

class RoleAdminController extends ApiController
{
    public function __construct(
        protected RoleWebService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $roles = Role::query()
            ->with('permissions')
            ->where('guard_name', 'web')
            ->latest()
            ->get();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->pluck('name')
            ->values();

        return $this->successResponse([
            'items' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                    'permissions' => $role->permissions->pluck('name')->values(),
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ];
            }),
            'permissions' => $permissions,
        ], __('messages.success'));
    }

    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');

        return $this->successResponse([
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->pluck('name')->values(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ], __('messages.success'));
    }

    public function store(StoreRoleWebRequest $request): JsonResponse
    {
        $role = $this->service->create($request->validated());
        $role->load('permissions');

        return $this->successResponse([
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->pluck('name')->values(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ], __('messages.created_successfully'), 201);
    }

    public function update(UpdateRoleWebRequest $request, Role $role): JsonResponse
    {
        $role = $this->service->update($role, $request->validated());
        $role->load('permissions');

        return $this->successResponse([
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->pluck('name')->values(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ], __('messages.updated_successfully'));
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->service->delete($role);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}