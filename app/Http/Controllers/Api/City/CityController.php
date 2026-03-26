<?php

namespace App\Http\Controllers\Api\City;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use App\Services\City\CityService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Http\Resources\City\CityResource;
use Illuminate\Http\Request;

class CityController extends ApiController
{
    public function __construct(
        protected CityService $cityService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $cities = $this->cityService->getAllPaginated((int) $request->get('per_page', 15));

        return $this->successResponse([
            'items' => CityResource::collection($cities->items()),
            'pagination' => [
                'current_page' => $cities->currentPage(),
                'last_page' => $cities->lastPage(),
                'per_page' => $cities->perPage(),
                'total' => $cities->total(),
            ],
        ]);
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        $city = $this->cityService->create($request->validated());

        return $this->successResponse(
            new CityResource($city),
            __('messages.created_successfully'),
            201
        );
    }

    public function show(City $city): JsonResponse
    {
        return $this->successResponse(new CityResource($city));
    }

    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        $city = $this->cityService->update($city, $request->validated());

        return $this->successResponse(
            new CityResource($city),
            __('messages.updated_successfully')
        );
    }

    public function destroy(City $city): JsonResponse
    {
        $this->cityService->delete($city);

        return $this->successResponse(null, __('messages.deleted_successfully'));
    }
}