<?php

namespace App\Http\Controllers\Api\Report;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Report\ServiceReportService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Report\ServiceReportResource;
use App\Http\Requests\Report\StoreServiceReportRequest;
use App\Http\Requests\Report\ResolveServiceReportRequest;
use App\Models\ServiceReport;

class ServiceReportController extends ApiController
{
    public function __construct(
        protected ServiceReportService $service
    ) {
    }

    public function store(StoreServiceReportRequest $request, Service $service): JsonResponse
    {
        $report = $this->service->create(
            $request->user(),
            $service,
            $request->validated()
        );

        return $this->successResponse(
            new ServiceReportResource($report),
            __('messages.created_successfully'),
            201
        );
    }

    public function index(): JsonResponse
    {
        $reports = $this->service->listForAdmin();

        return $this->successResponse(
            ServiceReportResource::collection($reports),
            __('messages.success')
        );
    }

    public function resolve(
        ResolveServiceReportRequest $request,
        ServiceReport $serviceReport
    ): JsonResponse {
        $report = $this->service->resolve(
            $request->user(),
            $serviceReport,
            $request->validated()['status']
        );

        return $this->successResponse(
            new ServiceReportResource($report),
            __('messages.updated_successfully')
        );
    }
}