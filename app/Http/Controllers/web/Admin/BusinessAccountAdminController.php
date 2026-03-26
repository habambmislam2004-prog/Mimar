<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use App\Services\BusinessAccount\BusinessAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessAccountAdminController extends Controller
{
    public function __construct(
        protected BusinessAccountService $service
    ) {
    }

    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        $selectedId = $request->get('selected');

        $items = $this->service->listForAdmin((int) $request->get('per_page', 12));
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

        return view('admin.business-accounts.index', [
            'items' => $items,
            'filteredItems' => $filteredItems,
            'selectedItem' => $selectedItem,
            'status' => $status,
        ]);
    }

    public function approve(Request $request, BusinessAccount $businessAccount): RedirectResponse
    {
        $this->service->approve($request->user(), $businessAccount);

        return redirect()
            ->route('admin.business-accounts.index', [
                'status' => $request->get('status', 'all'),
                'selected' => $businessAccount->id,
            ])
            ->with('success', __('messages.business_account_approved'));
    }

    public function reject(Request $request, BusinessAccount $businessAccount): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->service->reject(
            $request->user(),
            $businessAccount,
            $validated['rejection_reason']
        );

        return redirect()
            ->route('admin.business-accounts.index', [
                'status' => $request->get('status', 'all'),
                'selected' => $businessAccount->id,
            ])
            ->with('success', __('messages.business_account_rejected'));
    }
}