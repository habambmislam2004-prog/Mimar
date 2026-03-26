<?php

namespace App\Http\Controllers\Api\BusinessActivityType;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessActivityType;
use App\Http\Controllers\Api\ApiController;
use App\Services\BusinessActivityType\BusinessActivityTypeService;
use App\Http\Requests\BusinessActivityType\StoreBusinessActivityTypeRequest;
use App\Http\Requests\BusinessActivityType\UpdateBusinessActivityTypeRequest;
use App\Http\Resources\BusinessActivityType\BusinessActivityTypeResource;

class BusinessActivityTypeController extends ApiController
{
    public function __construct(
        protected BusinessActivityTypeService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->getAllPaginated((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => BusinessActivityTypeResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(StoreBusinessActivityTypeRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->successResponse(
            new BusinessActivityTypeResource($item),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(BusinessActivityType $businessActivityType): JsonResponse
    {
        return $this->successResponse(new BusinessActivityTypeResource($businessActivityType));
    }

    public function update(UpdateBusinessActivityTypeRequest $request, BusinessActivityType $businessActivityType): JsonResponse
    {
        $item = $this->service->update($businessActivityType, $request->validated());

        return $this->successResponse(
            new BusinessActivityTypeResource($item),
            __('messages.updated_successfully')
        );
    }

    public function destroy(BusinessActivityType $businessActivityType): JsonResponse
    {
        $this->service->delete($businessActivityType);

        return $this->successResponse(null, __('messages.deleted_successfully'));
    }
}