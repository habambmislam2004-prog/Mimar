<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $construction = Category::query()->where('name_en', 'Construction')->first();
        $painting = Category::query()->where('name_en', 'Painting')->first();
        $plastering = Category::query()->where('name_en', 'Plastering')->first();
        $ceramics = Category::query()->where('name_en', 'Ceramics')->first();

        $items = [
            ['category_id' => $construction?->id, 'name_ar' => 'بناء جدار', 'name_en' => 'Wall Construction', 'sort_order' => 1],
            ['category_id' => $construction?->id, 'name_ar' => 'ترميم', 'name_en' => 'Renovation', 'sort_order' => 2],
            ['category_id' => $painting?->id, 'name_ar' => 'دهان داخلي', 'name_en' => 'Interior Painting', 'sort_order' => 3],
            ['category_id' => $painting?->id, 'name_ar' => 'دهان خارجي', 'name_en' => 'Exterior Painting', 'sort_order' => 4],
            ['category_id' => $plastering?->id, 'name_ar' => 'تلبيس داخلي', 'name_en' => 'Interior Plastering', 'sort_order' => 5],
            ['category_id' => $ceramics?->id, 'name_ar' => 'سيراميك أرضيات', 'name_en' => 'Floor Ceramics', 'sort_order' => 6],
        ];

        foreach ($items as $item) {
            if (! $item['category_id']) {
                continue;
            }

            Subcategory::query()->firstOrCreate(
                [
                    'category_id' => $item['category_id'],
                    'name_ar' => $item['name_ar'],
                ],
                [
                    'name_en' => $item['name_en'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}