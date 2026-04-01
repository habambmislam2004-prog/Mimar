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
        $cities = City::query()->get();
        $materials = MaterialType::query()->get();

        $defaultPrices = [
            'block' => 4000,
            'cement' => 55000,
            'sand' => 120000,
            'paint' => 35000,
            'ceramic' => 90000,
            'adhesive' => 60000,
            'plaster_mix' => 50000,
        ];

        $cityPrices = [
            'damascus' => [
                'block' => 4200,
                'cement' => 58000,
                'sand' => 130000,
                'paint' => 38000,
                'ceramic' => 95000,
                'adhesive' => 63000,
                'plaster_mix' => 53000,
            ],
            'rif-dimashq' => [
                'block' => 4100,
                'cement' => 57000,
                'sand' => 128000,
                'paint' => 37000,
                'ceramic' => 93000,
                'adhesive' => 62000,
                'plaster_mix' => 52000,
            ],
            'aleppo' => [
                'block' => 3900,
                'cement' => 54000,
                'sand' => 118000,
                'paint' => 34000,
                'ceramic' => 88000,
                'adhesive' => 59000,
                'plaster_mix' => 49000,
            ],
            'homs' => [
                'block' => 3950,
                'cement' => 54500,
                'sand' => 119000,
                'paint' => 34500,
                'ceramic' => 88500,
                'adhesive' => 59500,
                'plaster_mix' => 49250,
            ],
            'hama' => [
                'block' => 3925,
                'cement' => 54250,
                'sand' => 117500,
                'paint' => 34300,
                'ceramic' => 88250,
                'adhesive' => 59250,
                'plaster_mix' => 49100,
            ],
            'latakia' => [
                'block' => 4300,
                'cement' => 59000,
                'sand' => 135000,
                'paint' => 39000,
                'ceramic' => 97000,
                'adhesive' => 65000,
                'plaster_mix' => 54000,
            ],
            'tartus' => [
                'block' => 4250,
                'cement' => 58500,
                'sand' => 133000,
                'paint' => 38500,
                'ceramic' => 96000,
                'adhesive' => 64000,
                'plaster_mix' => 53500,
            ],
            'idlib' => [
                'block' => 3850,
                'cement' => 53500,
                'sand' => 116000,
                'paint' => 33800,
                'ceramic' => 87500,
                'adhesive' => 58500,
                'plaster_mix' => 48750,
            ],
            'deir-ez-zor' => [
                'block' => 4500,
                'cement' => 61000,
                'sand' => 140000,
                'paint' => 40000,
                'ceramic' => 99000,
                'adhesive' => 67000,
                'plaster_mix' => 55500,
            ],
            'raqqa' => [
                'block' => 4400,
                'cement' => 60000,
                'sand' => 138000,
                'paint' => 39500,
                'ceramic' => 98000,
                'adhesive' => 66000,
                'plaster_mix' => 54800,
            ],
            'al-hasakah' => [
                'block' => 4450,
                'cement' => 60500,
                'sand' => 139000,
                'paint' => 39800,
                'ceramic' => 98500,
                'adhesive' => 66500,
                'plaster_mix' => 55200,
            ],
            'daraa' => [
                'block' => 4000,
                'cement' => 55000,
                'sand' => 121000,
                'paint' => 35000,
                'ceramic' => 90000,
                'adhesive' => 60000,
                'plaster_mix' => 50000,
            ],
            'as-suwayda' => [
                'block' => 4150,
                'cement' => 56500,
                'sand' => 126000,
                'paint' => 36500,
                'ceramic' => 92000,
                'adhesive' => 61500,
                'plaster_mix' => 51500,
            ],
            'quneitra' => [
                'block' => 4050,
                'cement' => 55500,
                'sand' => 123000,
                'paint' => 35500,
                'ceramic' => 90500,
                'adhesive' => 60500,
                'plaster_mix' => 50500,
            ],
        ];

        foreach ($cities as $city) {
            $cityKey = $this->resolveCityKey($city->name_ar, $city->name_en);

            foreach ($materials as $material) {
                $price = $cityPrices[$cityKey][$material->code]
                    ?? $defaultPrices[$material->code]
                    ?? 10000;

                CityMaterialPrice::query()->updateOrCreate(
                    [
                        'city_id' => $city->id,
                        'material_type_id' => $material->id,
                    ],
                    [
                        'price' => $price,
                        'currency' => 'SYP',
                        'effective_from' => now()->toDateString(),
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function resolveCityKey(?string $nameAr, ?string $nameEn): string
    {
        $nameAr = trim(mb_strtolower($nameAr ?? ''));
        $nameEn = trim(mb_strtolower($nameEn ?? ''));

        return match (true) {
            str_contains($nameAr, 'دمشق') || $nameEn === 'damascus' => 'damascus',
            str_contains($nameAr, 'ريف دمشق') || str_contains($nameEn, 'rif') || str_contains($nameEn, 'rural damascus') => 'rif-dimashq',
            str_contains($nameAr, 'حلب') || $nameEn === 'aleppo' => 'aleppo',
            str_contains($nameAr, 'حمص') || $nameEn === 'homs' => 'homs',
            str_contains($nameAr, 'حماة') || $nameEn === 'hama' => 'hama',
            str_contains($nameAr, 'اللاذقية') || $nameEn === 'latakia' => 'latakia',
            str_contains($nameAr, 'طرطوس') || $nameEn === 'tartus' => 'tartus',
            str_contains($nameAr, 'إدلب') || str_contains($nameAr, 'ادلب') || $nameEn === 'idlib' => 'idlib',
            str_contains($nameAr, 'دير الزور') || str_contains($nameEn, 'deir') => 'deir-ez-zor',
            str_contains($nameAr, 'الرقة') || str_contains($nameEn, 'raqqa') => 'raqqa',
            str_contains($nameAr, 'الحسكة') || str_contains($nameEn, 'hasakah') || str_contains($nameEn, 'hasaka') => 'al-hasakah',
            str_contains($nameAr, 'درعا') || $nameEn === 'daraa' => 'daraa',
            str_contains($nameAr, 'السويداء') || str_contains($nameEn, 'suwayda') || str_contains($nameEn, 'sweida') => 'as-suwayda',
            str_contains($nameAr, 'القنيطرة') || str_contains($nameEn, 'quneitra') || str_contains($nameEn, 'quneitra') => 'quneitra',
            default => 'default',
        };
    }
}