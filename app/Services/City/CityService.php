<?php

namespace App\Services\City;

use App\Models\City;

class CityService
{
    public function getAllPaginated(int $perPage = 15)
    {
        return City::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): City
    {
        return City::query()->create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(City $city, array $data): City
    {
        $city->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? $city->is_active,
            'sort_order' => $data['sort_order'] ?? $city->sort_order,
        ]);

        return $city->refresh();
    }

    public function delete(City $city): void
    {
        $city->delete();
    }
}