<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
{
    $status = $request->get('status');

    $orders = \App\Models\Order::query()
        ->with([
            'user',
            'service',
            'senderBusinessAccount',
            'receiverBusinessAccount',
            'rating',
        ])
        ->when($status, fn ($query) => $query->where('status', $status))
        ->latest()
        ->paginate(12)
        ->withQueryString();

    $stats = [
        'total' => \App\Models\Order::count(),
        'pending' => \App\Models\Order::where('status', 'pending')->count(),
        'accepted' => \App\Models\Order::where('status', 'accepted')->count(),
        'rejected' => \App\Models\Order::where('status', 'rejected')->count(),
        'cancelled' => \App\Models\Order::where('status', 'cancelled')->count(),
    ];

    $selectedOrder = null;

    if ($request->filled('selected')) {
        $selectedOrder = $orders->getCollection()->firstWhere('id', (int) $request->query('selected'));

        if (! $selectedOrder) {
            $selectedOrder = \App\Models\Order::query()
                ->with([
                    'user',
                    'service',
                    'senderBusinessAccount',
                    'receiverBusinessAccount',
                    'rating',
                ])
                ->find($request->query('selected'));
        }
    }

    if (! $selectedOrder) {
        $selectedOrder = $orders->first();
    }

    return view('admin.orders.index', compact('orders', 'stats', 'status', 'selectedOrder'));
}

    public function show(Order $order): View
    {
        $order->load([
            'user',
            'service',
            'senderBusinessAccount',
            'receiverBusinessAccount',
            'rating',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function accept(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', __('messages.order_not_pending'));
        }

        $order->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'rejected_at' => null,
            'cancelled_at' => null,
        ]);

        return back()->with('success', __('messages.order_accepted_successfully'));
    }

    public function reject(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->with('error', __('messages.order_not_pending'));
        }

        $order->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'accepted_at' => null,
            'cancelled_at' => null,
        ]);

        return back()->with('success', __('messages.order_rejected_successfully'));
    }

    public function destroy(Order $order): RedirectResponse
    {
        if ($order->rating) {
            $order->rating()->delete();
        }

        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}