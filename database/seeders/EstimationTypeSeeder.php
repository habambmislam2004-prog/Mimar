<?php

namespace Database\Seeders;

use App\Models\EstimationType;
use Illuminate\Database\Seeder;

class EstimationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'code' => 'wall_building',
                'name_ar' => 'بناء جدار',
                'name_en' => 'Wall Building',
                'unit_type' => 'area',
                'sort_order' => 1,
            ],
            [
                'code' => 'painting',
                'name_ar' => 'دهان',
                'name_en' => 'Painting',
                'unit_type' => 'area',
                'sort_order' => 2,
            ],
            [
                'code' => 'plastering',
                'name_ar' => 'تلبيس',
                'name_en' => 'Plastering',
                'unit_type' => 'area',
                'sort_order' => 3,
            ],
            [
                'code' => 'ceramic_installation',
                'name_ar' => 'تركيب سيراميك',
                'name_en' => 'Ceramic Installation',
                'unit_type' => 'area',
                'sort_order' => 4,
            ],
        ];

        foreach ($items as $item) {
            EstimationType::query()->firstOrCreate(
                ['code' => $item['code']],
                [
                    'name_ar' => $item['name_ar'],
                    'name_en' => $item['name_en'],
                    'unit_type' => $item['unit_type'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}