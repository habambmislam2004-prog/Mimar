<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\CityMaterialPrice;
use App\Http\Controllers\Api\ApiController;
use App\Services\Estimation\CityMaterialPriceService;
use App\Http\Resources\Estimation\CityMaterialPriceResource;
use App\Http\Requests\Estimation\StoreCityMaterialPriceRequest;
use App\Http\Requests\Estimation\UpdateCityMaterialPriceRequest;

class CityMaterialPriceController extends ApiController
{
    public function __construct(
        protected CityMaterialPriceService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => CityMaterialPriceResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(StoreCityMaterialPriceRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->successResponse(
            new CityMaterialPriceResource($item),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(CityMaterialPrice $cityMaterialPrice): JsonResponse
    {
        return $this->successResponse(
            new CityMaterialPriceResource($cityMaterialPrice->load(['city', 'materialType']))
        );
    }

    public function update(UpdateCityMaterialPriceRequest $request, CityMaterialPrice $cityMaterialPrice): JsonResponse
    {
        $item = $this->service->update($cityMaterialPrice, $request->validated());

        return $this->successResponse(
            new CityMaterialPriceResource($item),
            __('messages.updated_successfully')
        );
    }

    public function destroy(CityMaterialPrice $cityMaterialPrice): JsonResponse
    {
        $this->service->delete($cityMaterialPrice);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}