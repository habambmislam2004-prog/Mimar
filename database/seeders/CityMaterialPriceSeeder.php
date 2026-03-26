<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\MaterialType;
use App\Models\CityMaterialPrice;
use Illuminate\Database\Seeder;

class CityMaterialPriceSeeder extends Seeder
{
    public function run(): void
    {
        $cities = City::all();
        $materials = MaterialType::all();

        foreach ($cities as $city) {
            foreach ($materials as $material) {
                $basePrice = match ($material->code) {
                    'block' => 4000,
                    'cement' => 55000,
                    'sand' => 120000,
                    'paint' => 35000,
                    'ceramic' => 90000,
                    'adhesive' => 60000,
                    'plaster_mix' => 50000,
                    default => 10000,
                };

                CityMaterialPrice::query()->firstOrCreate(
                    [
                        'city_id' => $city->id,
                        'material_type_id' => $material->id,
                    ],
                    [
                        'price' => $basePrice,
                        'currency' => 'SYP',
                        'effective_from' => now()->toDateString(),
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}