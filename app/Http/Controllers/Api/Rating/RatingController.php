<?php

namespace App\Http\Controllers\Api\Rating;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Rating\RatingService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Rating\RatingResource;
use App\Http\Requests\Rating\StoreRatingRequest;

class RatingController extends ApiController
{
    public function __construct(
        protected RatingService $service
    ) {
    }

    public function store(StoreRatingRequest $request, Order $order): JsonResponse
    {
        $rating = $this->service->create(
            $request->user(),
            $order,
            $request->validated()
        );

        return $this->successResponse(
            new RatingResource($rating),
            __('messages.created_successfully'),
            201
        );
    }

    public function serviceRatings(Request $request, int $serviceId): JsonResponse
    {
        $ratings = $this->service->listServiceRatings($serviceId);

        return $this->successResponse(
            RatingResource::collection($ratings),
            __('messages.success')
        );
    }
}