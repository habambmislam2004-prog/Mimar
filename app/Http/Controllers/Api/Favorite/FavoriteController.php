<?php

namespace App\Http\Controllers\Api\Favorite;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Favorite\FavoriteService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Favorite\FavoriteResource;

class FavoriteController extends ApiController
{
    public function __construct(
        protected FavoriteService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $favorites = $this->service->list($request->user());

        return $this->successResponse(
            FavoriteResource::collection($favorites),
            __('messages.success')
        );
    }

    public function store(Request $request, Service $service): JsonResponse
    {
        $favorite = $this->service->add($request->user(), $service);

        return $this->successResponse(
            new FavoriteResource($favorite->load('service')),
            __('messages.created_successfully'),
            201
        );
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        $this->service->remove($request->user(), $service);

        return $this->successResponse(
            null,
            __('messages.deleted_successfully')
        );
    }
}