<?php

namespace App\Http\Controllers\Api\Subcategory;

use Illuminate\Http\Request;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use App\Services\Subcategory\SubcategoryService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Subcategory\UpdateSubcategoryRequest;
use App\Http\Resources\Subcategory\SubcategoryResource;

class SubcategoryController extends ApiController
{
    public function __construct(
        protected SubcategoryService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->getAllPaginated((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => SubcategoryResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(StoreSubcategoryRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->successResponse(
            new SubcategoryResource($item),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(Subcategory $subcategory): JsonResponse
    {
        return $this->successResponse(new SubcategoryResource($subcategory));
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory): JsonResponse
    {
        $item = $this->service->update($subcategory, $request->validated());

        return $this->successResponse(
            new SubcategoryResource($item),
            __('messages.updated_successfully')
        );
    }

    public function destroy(Subcategory $subcategory): JsonResponse
    {
        $this->service->delete($subcategory);

        return $this->successResponse(null, __('messages.deleted_successfully'));
    }
}