<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name_ar' => 'دمشق', 'name_en' => 'Damascus', 'sort_order' => 1],
            ['name_ar' => 'ريف دمشق', 'name_en' => 'Rif Damascus', 'sort_order' => 2],
            ['name_ar' => 'حلب', 'name_en' => 'Aleppo', 'sort_order' => 3],
            ['name_ar' => 'حمص', 'name_en' => 'Homs', 'sort_order' => 4],
            ['name_ar' => 'حماة', 'name_en' => 'Hama', 'sort_order' => 5],
            ['name_ar' => 'اللاذقية', 'name_en' => 'Latakia', 'sort_order' => 6],
            ['name_ar' => 'طرطوس', 'name_en' => 'Tartus', 'sort_order' => 7],
        ];

        foreach ($cities as $city) {
            City::query()->firstOrCreate(
                ['name_ar' => $city['name_ar']],
                [
                    'name_en' => $city['name_en'],
                    'is_active' => true,
                    'sort_order' => $city['sort_order'],
                ]
            );
        }
    }
}