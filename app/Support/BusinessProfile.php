<?php

namespace App\Support;

final class BusinessProfile
{
    public const TYPES = [
        'retail' => 'Retail / jual barang',
        'grocery' => 'Dagang / kelontong / sembako',
        'service' => 'Jasa / layanan',
        'food' => 'Kuliner / kafe / restoran',
        'mixed' => 'Usaha campuran',
    ];

    public static function normalize(?string $type): string
    {
        if (isset(self::TYPES[$type])) {
            return $type;
        }
        $legacy = strtolower($type ?? '');

        return match (true) {
            str_contains($legacy, 'cafe'), str_contains($legacy, 'kopi'), str_contains($legacy, 'f&b'), str_contains($legacy, 'resto') => 'food',
            str_contains($legacy, 'sembako'), str_contains($legacy, 'kelontong'), str_contains($legacy, 'minimarket') => 'grocery',
            str_contains($legacy, 'jasa'), str_contains($legacy, 'service') => 'service',
            str_contains($legacy, 'retail') => 'retail',
            default => 'mixed',
        };
    }

    public static function defaults(string $type): array
    {
        return match ($type) {
            'food' => ['goods', 'food'],
            'service' => ['services'],
            'mixed' => ['goods', 'services'],
            default => ['goods'],
        };
    }

    public static function samples(string $type): array
    {
        $items = match ($type) {
            'food' => [['Kopi Susu', 18000], ['Americano', 15000], ['Teh Lemon', 12000], ['Roti Bakar', 16000]],
            'service' => [['Potong rambut', 25000], ['Cuci kendaraan', 20000], ['Jasa setrika', 10000], ['Konsultasi', 50000]],
            'grocery' => [['Beras kemasan 5 kg', 75000], ['Minyak goreng 1 liter', 18000], ['Gula kemasan 1 kg', 17000], ['Sabun mandi', 5000]],
            default => [['Kaos polos', 50000], ['Tas belanja', 15000], ['Buku tulis', 8000], ['Pulpen', 5000]],
        };
        $rows = array_map(fn ($item) => ['name' => $item[0], 'price' => $item[1], 'category' => 'Contoh katalog', 'type' => $type === 'service' ? 'service' : 'product'], $items);
        if ($type === 'mixed') {
            $rows[3] = ['name' => 'Jasa pemasangan', 'price' => 25000, 'category' => 'Contoh layanan', 'type' => 'service'];
        }

        return $rows;
    }

    public static function catalog(string $type): string
    {
        return match ($type) {
            'food' => 'Menu', 'service' => 'Layanan', 'mixed' => 'Produk & layanan', default => 'Produk',
        };
    }
}
