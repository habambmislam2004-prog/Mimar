<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessActivityType;

class BusinessActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name_ar' => 'مقاول', 'name_en' => 'Contractor', 'sort_order' => 1],
            ['name_ar' => 'مورد مواد', 'name_en' => 'Material Supplier', 'sort_order' => 2],
            ['name_ar' => 'معلم دهان', 'name_en' => 'Painter', 'sort_order' => 3],
            ['name_ar' => 'معلم سيراميك', 'name_en' => 'Ceramic Installer', 'sort_order' => 4],
            ['name_ar' => 'معلم تلبيس', 'name_en' => 'Plaster Worker', 'sort_order' => 5],
        ];

        foreach ($items as $item) {
            BusinessActivityType::query()->firstOrCreate(
                ['name_ar' => $item['name_ar']],
                [
                    'name_en' => $item['name_en'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}