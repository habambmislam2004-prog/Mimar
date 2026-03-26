<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityAdminController extends Controller
{
    public function index(Request $request): View
    {
        $cities = City::query()
            ->withCount('businessAccounts')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 12))
            ->withQueryString();

        $selectedCity = null;

        if ($request->filled('selected')) {
            $selectedCity = City::query()
                ->with([
                    'businessAccounts.user',
                    'businessAccounts.activityType',
                ])
                ->find((int) $request->get('selected'));
        }

        if (! $selectedCity && $cities->count()) {
            $selectedCity = City::query()
                ->with([
                    'businessAccounts.user',
                    'businessAccounts.activityType',
                ])
                ->find($cities->first()->id);
        }

        return view('admin.cities.index', compact('cities', 'selectedCity'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'in:0,1'],
        ]);

        $city = City::query()->create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.cities.index', ['selected' => $city->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'in:0,1'],
        ]);

        $city->update([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.cities.index', ['selected' => $city->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(City $city): RedirectResponse
    {
        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}