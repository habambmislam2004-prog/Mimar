<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EstimationType;
use App\Http\Controllers\Api\ApiController;
use App\Services\Estimation\EstimationTypeService;
use App\Http\Resources\Estimation\EstimationTypeResource;
use App\Http\Requests\Estimation\StoreEstimationTypeRequest;
use App\Http\Requests\Estimation\UpdateEstimationTypeRequest;

class EstimationTypeController extends ApiController
{
    public function __construct(
        protected EstimationTypeService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => EstimationTypeResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(StoreEstimationTypeRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->successResponse(
            new EstimationTypeResource($item),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(EstimationType $estimationType): JsonResponse
    {
        return $this->successResponse(
            new EstimationTypeResource($estimationType)
        );
    }

    public function update(UpdateEstimationTypeRequest $request, EstimationType $estimationType): JsonResponse
    {
        $item = $this->service->update($estimationType, $request->validated());

        return $this->successResponse(
            new EstimationTypeResource($item),
            __('messages.updated_successfully')
        );
    }

    public function destroy(EstimationType $estimationType): JsonResponse
    {
        $this->service->delete($estimationType);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}