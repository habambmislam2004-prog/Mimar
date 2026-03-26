<?php

namespace App\Services\Estimation;

use App\Models\MaterialType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaterialTypeService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return MaterialType::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): MaterialType
    {
        return MaterialType::query()->create([
            'code' => $data['code'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'base_unit' => $data['base_unit'],
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(MaterialType $materialType, array $data): MaterialType
    {
        $materialType->update([
            'code' => $data['code'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'base_unit' => $data['base_unit'],
            'is_active' => $data['is_active'] ?? $materialType->is_active,
            'sort_order' => $data['sort_order'] ?? $materialType->sort_order,
        ]);

        return $materialType->refresh();
    }

    public function delete(MaterialType $materialType): void
    {
        $materialType->delete();
    }
}