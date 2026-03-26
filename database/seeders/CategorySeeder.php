<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name_ar' => 'بناء', 'name_en' => 'Construction', 'icon' => 'construction.png', 'sort_order' => 1],
            ['name_ar' => 'دهان', 'name_en' => 'Painting', 'icon' => 'painting.png', 'sort_order' => 2],
            ['name_ar' => 'تلبيس', 'name_en' => 'Plastering', 'icon' => 'plastering.png', 'sort_order' => 3],
            ['name_ar' => 'سيراميك', 'name_en' => 'Ceramics', 'icon' => 'ceramics.png', 'sort_order' => 4],
        ];

        foreach ($items as $item) {
            Category::query()->firstOrCreate(
                ['name_ar' => $item['name_ar']],
                [
                    'name_en' => $item['name_en'],
                    'icon' => $item['icon'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}