<?php

namespace App\Services\Estimation;

use App\Models\CityMaterialPrice;
use App\Exceptions\DomainException;

class MaterialPriceResolverService
{
    public function getPrice(int $cityId, int $materialTypeId): float
    {
        $price = CityMaterialPrice::query()
            ->where('city_id', $cityId)
            ->where('material_type_id', $materialTypeId)
            ->where('is_active', true)
            ->value('price');

        if ($price === null) {
            throw new DomainException(__('messages.material_price_not_found'));
        }

        return (float) $price;
    }
}