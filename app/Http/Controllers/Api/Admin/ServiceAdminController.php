<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Service;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use App\Services\Service\ServiceService;
use App\Http\Requests\Service\ApproveServiceRequest;
use App\Http\Requests\Service\RejectServiceRequest;
use App\Http\Resources\Service\ServiceResource;

class ServiceAdminController extends ApiController
{
    public function __construct(
        protected ServiceService $service
    ) {
    }

    public function approve(ApproveServiceRequest $request, Service $service): JsonResponse
    {
        $service = $this->service->approve($request->user(), $service);

        return $this->successResponse(
            new ServiceResource($service),
            __('messages.service_approved')
        );
    }

    public function reject(RejectServiceRequest $request, Service $service): JsonResponse
    {
        $service = $this->service->reject(
            $request->user(),
            $service,
            $request->validated()['rejection_reason']
        );

        return $this->successResponse(
            new ServiceResource($service),
            __('messages.service_rejected')
        );
    }
}