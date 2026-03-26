<?php

namespace App\Services\BusinessActivityType;

use App\Models\BusinessActivityType;

class BusinessActivityTypeService
{
    public function getAllPaginated(int $perPage = 15)
    {
        return BusinessActivityType::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): BusinessActivityType
    {
        return BusinessActivityType::query()->create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(BusinessActivityType $type, array $data): BusinessActivityType
    {
        $type->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? $type->is_active,
            'sort_order' => $data['sort_order'] ?? $type->sort_order,
        ]);

        return $type->refresh();
    }

    public function delete(BusinessActivityType $type): void
    {
        $type->delete();
    }
}