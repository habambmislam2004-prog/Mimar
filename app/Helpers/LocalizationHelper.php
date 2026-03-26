<?php

namespace App\Helpers;

class LocalizationHelper
{
    public static function getLocalizedName(
        ?string $nameAr,
        ?string $nameEn,
        string $locale = 'ar'
    ): ?string {
        return $locale === 'en'
            ? ($nameEn ?: $nameAr)
            : ($nameAr ?: $nameEn);
    }
}