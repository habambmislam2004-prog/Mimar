<?php

namespace Database\Seeders;

use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['code' => 'block', 'name_ar' => 'بلوك', 'name_en' => 'Block', 'base_unit' => 'piece', 'sort_order' => 1],
            ['code' => 'cement', 'name_ar' => 'اسمنت', 'name_en' => 'Cement', 'base_unit' => 'bag', 'sort_order' => 2],
            ['code' => 'sand', 'name_ar' => 'رمل', 'name_en' => 'Sand', 'base_unit' => 'm3', 'sort_order' => 3],
            ['code' => 'paint', 'name_ar' => 'دهان', 'name_en' => 'Paint', 'base_unit' => 'liter', 'sort_order' => 4],
            ['code' => 'ceramic', 'name_ar' => 'سيراميك', 'name_en' => 'Ceramic', 'base_unit' => 'm2', 'sort_order' => 5],
            ['code' => 'adhesive', 'name_ar' => 'لاصق', 'name_en' => 'Adhesive', 'base_unit' => 'bag', 'sort_order' => 6],
            ['code' => 'plaster_mix', 'name_ar' => 'خليط تلبيس', 'name_en' => 'Plaster Mix', 'base_unit' => 'bag', 'sort_order' => 7],
        ];

        foreach ($items as $item) {
            MaterialType::query()->firstOrCreate(
                ['code' => $item['code']],
                [
                    'name_ar' => $item['name_ar'],
                    'name_en' => $item['name_en'],
                    'base_unit' => $item['base_unit'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}