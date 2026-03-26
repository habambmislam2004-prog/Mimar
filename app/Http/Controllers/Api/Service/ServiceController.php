<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\Service;
use App\Models\BusinessAccount;
use Illuminate\Http\JsonResponse;
use App\Services\Service\ServiceService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\Service\ServiceResource;

class ServiceController extends ApiController
{
    public function __construct(
        protected ServiceService $service
    ) {
    }

    public function index(BusinessAccount $businessAccount): JsonResponse
    {
        $services = $this->service->listForUser($businessAccount);

        return $this->successResponse(
            ServiceResource::collection($services),
            __('messages.success')
        );
    }

    public function store(StoreServiceRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $service = $this->service->create($businessAccount, $request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            __('messages.created_successfully'),
            201
        );
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service = $this->service->update($service, $request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            __('messages.updated_successfully')
        );
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->service->delete($service);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}