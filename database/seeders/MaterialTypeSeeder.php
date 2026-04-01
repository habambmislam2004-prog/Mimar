<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaterialType;

class MaterialTypeSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'code' => 'block',
                'name_ar' => 'بلوك',
                'name_en' => 'Block',
                'base_unit' => 'piece',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'cement',
                'name_ar' => 'اسمنت',
                'name_en' => 'Cement',
                'base_unit' => 'bag',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'sand',
                'name_ar' => 'رمل',
                'name_en' => 'Sand',
                'base_unit' => 'm3',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'paint',
                'name_ar' => 'دهان',
                'name_en' => 'Paint',
                'base_unit' => 'bucket',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'ceramic',
                'name_ar' => 'سيراميك',
                'name_en' => 'Ceramic',
                'base_unit' => 'm2',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'adhesive',
                'name_ar' => 'لاصق',
                'name_en' => 'Adhesive',
                'base_unit' => 'bag',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'code' => 'plaster_mix',
                'name_ar' => 'خليط تلبيس',
                'name_en' => 'Plaster Mix',
                'base_unit' => 'bag',
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($materials as $material) {
            MaterialType::query()->updateOrCreate(
                ['code' => $material['code']],
                [
                    'name_ar' => $material['name_ar'],
                    'name_en' => $material['name_en'],
                    'base_unit' => $material['base_unit'],
                    'is_active' => $material['is_active'],
                    'sort_order' => $material['sort_order'],
                ]
            );
        }
    }
}