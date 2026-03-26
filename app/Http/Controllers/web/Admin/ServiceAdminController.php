<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\Service\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceAdminController extends Controller
{
    public function __construct(
        protected ServiceService $serviceManager
    ) {
    }

    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        $selectedId = $request->get('selected');

        $items = Service::query()
            ->with(['businessAccount', 'category', 'subcategory', 'images'])
            ->latest()
            ->paginate((int) $request->get('per_page', 12))
            ->withQueryString();

        $collection = collect($items->items());

        $filteredItems = match ($status) {
            'pending' => $collection->where('status', 'pending')->values(),
            'approved' => $collection->where('status', 'approved')->values(),
            'rejected' => $collection->where('status', 'rejected')->values(),
            default => $collection->values(),
        };

        $selectedItem = null;

        if ($selectedId) {
            $selectedItem = $filteredItems->firstWhere('id', (int) $selectedId);
        }

        if (! $selectedItem) {
            $selectedItem = $filteredItems->first();
        }

        return view('admin.services.index', [
            'items' => $items,
            'filteredItems' => $filteredItems,
            'selectedItem' => $selectedItem,
            'status' => $status,
        ]);
    }

    public function approve(Request $request, Service $service): RedirectResponse
    {
        $this->serviceManager->approve($request->user(), $service);

        return redirect()
            ->route('admin.services.index', [
                'status' => $request->get('status', 'all'),
                'selected' => $service->id,
            ])
            ->with('success', __('messages.service_approved'));
    }

    public function reject(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->serviceManager->reject(
            $request->user(),
            $service,
            $validated['rejection_reason']
        );

        return redirect()
            ->route('admin.services.index', [
                'status' => $request->get('status', 'all'),
                'selected' => $service->id,
            ])
            ->with('success', __('messages.service_rejected'));
    }
}