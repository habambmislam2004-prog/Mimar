<?php

namespace App\Http\Controllers\Api\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Notification\DeleteDeviceTokenRequest;
use App\Services\Notification\DeviceTokenService;
use App\Http\Requests\Notification\StoreDeviceTokenRequest;

class DeviceTokenController extends ApiController
{
    public function __construct(
        protected DeviceTokenService $service
    ) {
    }

    public function store(StoreDeviceTokenRequest $request): JsonResponse
    {
        $deviceToken = $this->service->store(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            $deviceToken,
            __('messages.created_successfully'),
            201
        );
    }

   public function destroy(DeleteDeviceTokenRequest $request): JsonResponse
    {
    $this->service->delete(
        $request->user(),
        $request->validated()['token']
    );

    return $this->successResponse(
        null,
        __('messages.deleted_successfully')
    );
   }
}