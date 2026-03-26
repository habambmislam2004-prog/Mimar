<?php

namespace App\Http\Controllers\Api\Order;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Order\OrderService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Order\OrderResource;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\AcceptOrderRequest;
use App\Http\Requests\Order\RejectOrderRequest;
use App\Http\Requests\Order\CancelOrderRequest;

class OrderController extends ApiController
{
    public function __construct(
        protected OrderService $service
    ) {
    }

    public function sent(Request $request): JsonResponse
    {
        $orders = $this->service->listSentOrders($request->user());

        return $this->successResponse(
            OrderResource::collection($orders),
            __('messages.success')
        );
    }

    public function received(Request $request): JsonResponse
    {
        $orders = $this->service->listReceivedOrders($request->user());

        return $this->successResponse(
            OrderResource::collection($orders),
            __('messages.success')
        );
    }

    public function store(StoreOrderRequest $request, Service $service): JsonResponse
    {
        $order = $this->service->create(
            $request->user(),
            $service,
            $request->validated()
        );

        return $this->successResponse(
            new OrderResource($order),
            __('messages.created_successfully'),
            201
        );
    }

    public function accept(AcceptOrderRequest $request, Order $order): JsonResponse
    {
        $order = $this->service->accept($request->user(), $order);

        return $this->successResponse(
            new OrderResource($order),
            __('messages.order_accepted')
        );
    }

    public function reject(RejectOrderRequest $request, Order $order): JsonResponse
    {
        $order = $this->service->reject($request->user(), $order);

        return $this->successResponse(
            new OrderResource($order),
            __('messages.order_rejected')
        );
    }

    public function cancel(CancelOrderRequest $request, Order $order): JsonResponse
    {
        $order = $this->service->cancel($request->user(), $order);

        return $this->successResponse(
            new OrderResource($order),
            __('messages.order_cancelled')
        );
    }
}