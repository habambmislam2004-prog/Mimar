<?php

namespace App\Services\Estimation;

use App\Models\CityMaterialPrice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CityMaterialPriceService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return CityMaterialPrice::query()
            ->with(['city', 'materialType'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): CityMaterialPrice
    {
        return CityMaterialPrice::query()->create([
            'city_id' => $data['city_id'],
            'material_type_id' => $data['material_type_id'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'SYP',
            'effective_from' => $data['effective_from'] ?? now()->toDateString(),
            'is_active' => $data['is_active'] ?? true,
        ])->load(['city', 'materialType']);
    }

    public function update(CityMaterialPrice $cityMaterialPrice, array $data): CityMaterialPrice
    {
        $cityMaterialPrice->update([
            'city_id' => $data['city_id'],
            'material_type_id' => $data['material_type_id'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? $cityMaterialPrice->currency,
            'effective_from' => $data['effective_from'] ?? $cityMaterialPrice->effective_from,
            'is_active' => $data['is_active'] ?? $cityMaterialPrice->is_active,
        ]);

        return $cityMaterialPrice->refresh()->load(['city', 'materialType']);
    }

    public function delete(CityMaterialPrice $cityMaterialPrice): void
    {
        $cityMaterialPrice->delete();
    }
}