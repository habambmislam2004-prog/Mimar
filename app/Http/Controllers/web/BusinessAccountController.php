<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\BusinessAccount\StoreBusinessAccountWebRequest;
use App\Http\Requests\Web\BusinessAccount\UpdateBusinessAccountWebRequest;
use App\Models\BusinessAccount;
use App\Models\BusinessActivityType;
use App\Models\City;
use App\Services\Web\BusinessAccountWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BusinessAccountController extends Controller
{
    public function __construct(
        protected BusinessAccountWebService $service
    ) {
    }

    public function index(): View
    {
        $businessAccounts = BusinessAccount::with([
            'city',
            'activityType',
            'images',
            'documents',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $latestBusinessAccount = $businessAccounts->first();

        $cities = City::latest()->get();
        $activityTypes = BusinessActivityType::latest()->get();

        return view('public.business-account.index', compact(
            'businessAccounts',
            'latestBusinessAccount',
            'cities',
            'activityTypes'
        ));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('business-account.index', ['new' => 1]);
    }

    public function store(StoreBusinessAccountWebRequest $request): RedirectResponse
    {
        $this->service->create(
            $request->user(),
            $request->validated(),
            $request->file('images', []),
            $request->file('documents', [])
        );

        return redirect()
            ->route('business-account.index')
            ->with('success', __('messages.business_account_submitted'));
    }

    public function update(UpdateBusinessAccountWebRequest $request, BusinessAccount $businessAccount): RedirectResponse
    {
        $this->service->update(
            $businessAccount,
            $request->validated(),
            $request->file('images', []),
            $request->file('documents', [])
        );

        return redirect()
            ->route('business-account.index')
            ->with('success', __('messages.business_account_updated'));
    }
}