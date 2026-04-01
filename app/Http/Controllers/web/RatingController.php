<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\StoreRatingRequest;
use App\Models\Order;
use App\Services\Rating\RatingService;
use Illuminate\Http\RedirectResponse;

class RatingController extends Controller
{
    public function __construct(
        protected RatingService $service
    ) {
    }

    public function store(StoreRatingRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->service->create(
                $request->user(),
                $order,
                $request->validated()
            );

            return redirect()
                ->route('orders.index', ['tab' => 'sent'])
                ->with('success', __('messages.created_successfully'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function index(\App\Models\Service $service): RedirectResponse
    {
        return redirect()->route('services.show', $service->id);
    }
}