<?php

namespace App\Http\Controllers\Api\BusinessAccount;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessAccount;
use App\Services\BusinessAccount\BusinessAccountService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\BusinessAccount\BusinessAccountResource;
use App\Http\Requests\BusinessAccount\StoreBusinessAccountRequest;
use App\Http\Requests\BusinessAccount\UpdateBusinessAccountRequest;

class BusinessAccountController extends ApiController
{
    public function __construct(
        protected BusinessAccountService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->listForUser(
            $request->user(),
            (int) $request->get('per_page', 15)
        );

        return $this->successResponse([
            'items' => BusinessAccountResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(StoreBusinessAccountRequest $request): JsonResponse
    {
        $businessAccount = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            new BusinessAccountResource($businessAccount),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(BusinessAccount $businessAccount): JsonResponse
    {
        return $this->successResponse(
            new BusinessAccountResource(
                $businessAccount->load(['city', 'activityType', 'images', 'documents'])
            )
        );
    }

    public function update(UpdateBusinessAccountRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $businessAccount = $this->service->update(
            $request->user(),
            $businessAccount,
            $request->validated()
        );

        return $this->successResponse(
            new BusinessAccountResource($businessAccount),
            __('messages.updated_successfully')
        );
    }

    public function destroy(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $this->service->delete($request->user(), $businessAccount);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}