<?php

namespace App\Helpers;

class MoneyHelper
{
    public static function format(float|int|string $amount, string $currency = 'SYP'): string
    {
        return number_format((float) $amount, 2) . ' ' . $currency;
    }
}