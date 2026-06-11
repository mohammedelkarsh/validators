<?php

declare(strict_types=1);

namespace Validators\Eg;

final class Governorates
{
    /** @var array<int, string> */
    private const CODES = [
        1 => 'Cairo',
        2 => 'Alexandria',
        3 => 'Port Said',
        4 => 'Suez',
        11 => 'Damietta',
        12 => 'Dakahlia',
        13 => 'Sharqia',
        14 => 'Qalyubia',
        15 => 'Kafr El Sheikh',
        16 => 'Gharbia',
        17 => 'Monufia',
        18 => 'Beheira',
        19 => 'Ismailia',
        21 => 'Giza',
        22 => 'Beni Suef',
        23 => 'Fayoum',
        24 => 'Minya',
        25 => 'Asyut',
        26 => 'Sohag',
        27 => 'Qena',
        28 => 'Aswan',
        29 => 'Luxor',
        32 => 'New Valley',
        33 => 'Matrouh',
        34 => 'North Sinai',
        35 => 'South Sinai',
        88 => 'Foreign',
    ];

    /** @return list<int> */
    public static function codes(): array
    {
        return array_keys(self::CODES);
    }

    public static function isValid(int $code): bool
    {
        return array_key_exists($code, self::CODES);
    }

    public static function name(int $code): ?string
    {
        return self::CODES[$code] ?? null;
    }
}
