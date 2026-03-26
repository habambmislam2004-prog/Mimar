<?php

namespace App\Services\Category;

use App\Models\Category;

class CategoryService
{
    public function getAllPaginated(int $perPage = 15)
    {
        return Category::query()
            ->with('subcategories')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Category
    {
        return Category::query()->create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'icon' => $data['icon'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'icon' => $data['icon'] ?? $category->icon,
            'is_active' => $data['is_active'] ?? $category->is_active,
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
        ]);

        return $category->refresh()->load('subcategories');
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}