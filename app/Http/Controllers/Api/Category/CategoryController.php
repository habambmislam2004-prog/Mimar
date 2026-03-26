<?php

namespace App\Http\Controllers\Api\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Category\CategoryService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;

class CategoryController extends ApiController
{
    public function __construct(
        protected CategoryService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->getAllPaginated((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => CategoryResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->successResponse(
            new CategoryResource($item),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(Category $category): JsonResponse
    {
        return $this->successResponse(
            new CategoryResource($category->load('subcategories'))
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $item = $this->service->update($category, $request->validated());

        return $this->successResponse(
            new CategoryResource($item),
            __('messages.updated_successfully')
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->service->delete($category);

        return $this->successResponse(null, __('messages.deleted_successfully'));
    }
}