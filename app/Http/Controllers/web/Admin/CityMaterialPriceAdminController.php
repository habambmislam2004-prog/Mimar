<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityMaterialPrice;
use App\Models\MaterialType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityMaterialPriceAdminController extends Controller
{
    public function index(Request $request): View
    {
        $prices = CityMaterialPrice::query()
            ->with(['city', 'materialType'])
            ->orderByDesc('id')
            ->paginate((int) $request->query('per_page', 12))
            ->withQueryString();

        $priceItems = collect($prices->items());

        $selectedPrice = null;

        if ($request->filled('selected')) {
            $selectedPrice = $priceItems->firstWhere('id', (int) $request->query('selected'));
        }

        if (! $selectedPrice) {
            $selectedPrice = $priceItems->first();
        }

        $cities = City::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_ar')
            ->get();

        $materialTypes = MaterialType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_ar')
            ->get();

        return view('admin.city-material-prices.index', compact(
            'prices',
            'selectedPrice',
            'cities',
            'materialTypes'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'material_type_id' => ['required', 'exists:material_types,id'],
            'price' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'string', 'max:10'],
            'effective_from' => ['nullable', 'date'],
            'is_active' => ['required', 'in:0,1'],
        ]);

        $existing = CityMaterialPrice::query()
            ->where('city_id', $validated['city_id'])
            ->where('material_type_id', $validated['material_type_id'])
            ->first();

        if ($existing) {
            return redirect()
                ->route('admin.city-material-prices.index', ['selected' => $existing->id])
                ->withErrors([
                    'material_type_id' => app()->getLocale() === 'ar'
                        ? 'يوجد سعر مسجل مسبقًا لهذه المادة داخل هذه المدينة.'
                        : 'A price already exists for this material in this city.',
                ]);
        }

        $price = CityMaterialPrice::query()->create([
            'city_id' => $validated['city_id'],
            'material_type_id' => $validated['material_type_id'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'effective_from' => $validated['effective_from'] ?? now()->toDateString(),
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.city-material-prices.index', ['selected' => $price->id])
            ->with('success', __('messages.created_successfully'));
    }

    public function update(Request $request, CityMaterialPrice $cityMaterialPrice): RedirectResponse
    {
        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'material_type_id' => ['required', 'exists:material_types,id'],
            'price' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'string', 'max:10'],
            'effective_from' => ['nullable', 'date'],
            'is_active' => ['required', 'in:0,1'],
        ]);

        $duplicate = CityMaterialPrice::query()
            ->where('city_id', $validated['city_id'])
            ->where('material_type_id', $validated['material_type_id'])
            ->where('id', '!=', $cityMaterialPrice->id)
            ->first();

        if ($duplicate) {
            return redirect()
                ->route('admin.city-material-prices.index', ['selected' => $cityMaterialPrice->id])
                ->withErrors([
                    'material_type_id' => app()->getLocale() === 'ar'
                        ? 'يوجد سعر آخر مسجل لنفس المادة داخل هذه المدينة.'
                        : 'Another price already exists for the same material in this city.',
                ]);
        }

        $cityMaterialPrice->update([
            'city_id' => $validated['city_id'],
            'material_type_id' => $validated['material_type_id'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'effective_from' => $validated['effective_from'] ?? $cityMaterialPrice->effective_from,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.city-material-prices.index', ['selected' => $cityMaterialPrice->id])
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(CityMaterialPrice $cityMaterialPrice): RedirectResponse
    {
        $cityMaterialPrice->delete();

        return redirect()
            ->route('admin.city-material-prices.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}