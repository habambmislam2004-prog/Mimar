<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\MaterialType;
use App\Http\Controllers\Api\ApiController;
use App\Services\Estimation\MaterialTypeService;
use App\Http\Resources\Estimation\MaterialTypeResource;
use App\Http\Requests\Estimation\StoreMaterialTypeRequest;
use App\Http\Requests\Estimation\UpdateMaterialTypeRequest;

class MaterialTypeController extends ApiController
{
    public function __construct(
        protected MaterialTypeService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => MaterialTypeResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(StoreMaterialTypeRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->successResponse(
            new MaterialTypeResource($item),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(MaterialType $materialType): JsonResponse
    {
        return $this->successResponse(
            new MaterialTypeResource($materialType)
        );
    }

    public function update(UpdateMaterialTypeRequest $request, MaterialType $materialType): JsonResponse
    {
        $item = $this->service->update($materialType, $request->validated());

        return $this->successResponse(
            new MaterialTypeResource($item),
            __('messages.updated_successfully')
        );
    }

    public function destroy(MaterialType $materialType): JsonResponse
    {
        $this->service->delete($materialType);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}