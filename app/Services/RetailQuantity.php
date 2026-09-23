<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class RetailQuantity
{
    public static function ticks(int|float|string $value): int
    {
        return (int) round((float) $value * 1000);
    }

    public static function number(int $ticks): int|float
    {
        return $ticks % 1000 === 0 ? intdiv($ticks, 1000) : $ticks / 1000;
    }

    public static function base(int|float|string $quantity, int|float|string $factor): int|float
    {
        $ticks = self::ticks($quantity) * self::ticks($factor);
        if ($ticks % 1000 !== 0 || $ticks <= 0 || $ticks > 1000000000000000) {
            throw ValidationException::withMessages(['quantity' => 'Hasil konversi harus positif, maksimal 1 miliar, dan paling kecil 0,001 satuan dasar.']);
        }

        return self::number(intdiv($ticks, 1000));
    }

    public static function requireWhole(int|float|string $quantity, bool $allowFraction): void
    {
        if (! $allowFraction && self::ticks($quantity) % 1000 !== 0) {
            throw ValidationException::withMessages(['quantity' => 'Produk ini hanya menerima jumlah bulat. Aktifkan barang timbang untuk jumlah pecahan.']);
        }
    }
}
