<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\SystemRole;
use App\Http\Controllers\Api\ApiController;
use App\Services\Web\UserWebService;
use App\Http\Requests\Web\User\StoreUserWebRequest;
use App\Http\Requests\Web\User\UpdateUserWebRequest;

class UserAdminController extends ApiController
{
    public function __construct(
        protected UserWebService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $users = $this->service->paginate((int) $request->query('per_page', 12));

        return $this->successResponse([
            'items' => collect($users->items())->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => $user->status ?? null,
                    'roles' => method_exists($user, 'getRoleNames')
                        ? $user->getRoleNames()->values()
                        : [],
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            }),
            'roles' => [
                SystemRole::ADMIN->value,
                SystemRole::USER->value,
            ],
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], __('messages.success'));
    }

    public function store(StoreUserWebRequest $request): JsonResponse
    {
        $user = $this->service->create($request->validated());

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status ?? null,
            'roles' => method_exists($user, 'getRoleNames')
                ? $user->getRoleNames()->values()
                : [],
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], __('messages.created_successfully'), 201);
    }

    public function show(User $user): JsonResponse
    {
        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status ?? null,
            'roles' => method_exists($user, 'getRoleNames')
                ? $user->getRoleNames()->values()
                : [],
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], __('messages.success'));
    }

    public function update(UpdateUserWebRequest $request, User $user): JsonResponse
    {
        $user = $this->service->update($user, $request->validated());

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status ?? null,
            'roles' => method_exists($user, 'getRoleNames')
                ? $user->getRoleNames()->values()
                : [],
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], __('messages.updated_successfully'));
    }

    public function destroy(User $user): JsonResponse
    {
        $this->service->delete($user);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}