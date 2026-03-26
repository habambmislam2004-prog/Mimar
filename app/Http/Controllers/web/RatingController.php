<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($order->status !== 'accepted') {
            return back()->with('error', __('messages.order_not_eligible_for_rating'));
        }

        if ($order->senderBusinessAccount?->user_id !== $request->user()->id) {
            return back()->with('error', __('messages.forbidden'));
        }

        if ($order->rating) {
            return back()->with('error', __('messages.rating_already_exists'));
        }

        Rating::query()->create([
            'order_id' => $order->id,
            'service_id' => $order->service_id,
            'user_id' => $request->user()->id,
            'score' => $validated['score'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()
            ->route('orders.index', ['tab' => 'sent'])
            ->with('success', __('messages.created_successfully'));
    }
}