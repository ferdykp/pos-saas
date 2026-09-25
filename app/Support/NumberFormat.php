<?php

namespace App\Support;

final class NumberFormat
{
    /** Format kuantitas tanpa nol desimal palsu: 6 -> 6, 3.500 -> 3,5. */
    public static function quantity(mixed $value, int $maxDecimals = 3): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $number = (float) $value;
        $formatted = number_format($number, $maxDecimals, ',', '.');
        $formatted = rtrim(rtrim($formatted, '0'), ',');

        return $formatted === '-0' ? '0' : $formatted;
    }
}
