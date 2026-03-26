<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceReportAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        $selectedId = $request->get('selected');

        $items = ServiceReport::query()
            ->with([
                'user',
                'service.businessAccount',
                'service.category',
                'service.subcategory',
                'service.images',
                'reviewedBy',
            ])
            ->latest()
            ->paginate((int) $request->get('per_page', 12))
            ->withQueryString();

        $collection = collect($items->items());

        $filteredItems = match ($status) {
            'pending' => $collection->where('status', 'pending')->values(),
            'resolved' => $collection->where('status', 'resolved')->values(),
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

        return view('admin.reports.index', [
            'items' => $items,
            'filteredItems' => $filteredItems,
            'selectedItem' => $selectedItem,
            'status' => $status,
        ]);
    }

    public function resolve(Request $request, ServiceReport $serviceReport): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:resolved,rejected'],
        ]);

        $serviceReport->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.reports.index', [
                'status' => $request->get('current_status', 'all'),
                'selected' => $serviceReport->id,
            ])
            ->with('success', __('messages.updated_successfully'));
    }
}