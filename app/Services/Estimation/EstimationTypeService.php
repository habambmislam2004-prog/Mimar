<?php

namespace App\Services\Estimation;

use App\Models\EstimationType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EstimationTypeService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return EstimationType::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): EstimationType
    {
        return EstimationType::query()->create([
            'code' => $data['code'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'unit_type' => $data['unit_type'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(EstimationType $estimationType, array $data): EstimationType
    {
        $estimationType->update([
            'code' => $data['code'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'unit_type' => $data['unit_type'] ?? null,
            'is_active' => $data['is_active'] ?? $estimationType->is_active,
            'sort_order' => $data['sort_order'] ?? $estimationType->sort_order,
        ]);

        return $estimationType->refresh();
    }

    public function delete(EstimationType $estimationType): void
    {
        $estimationType->delete();
    }
}