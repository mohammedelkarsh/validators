<?php

declare(strict_types=1);

namespace Validators\Core;

final class Normalizer
{
    public static function digitsOnly(mixed $value): string
    {
        if (! is_scalar($value) && $value !== null) {
            return '';
        }

        $normalized = self::toLatinDigits((string) $value);

        return preg_replace('/\D+/', '', $normalized) ?? '';
    }

    public static function alphanumericUpper(mixed $value): string
    {
        if (! is_scalar($value) && $value !== null) {
            return '';
        }

        $normalized = self::toLatinDigits((string) $value);
        $normalized = preg_replace('/\s+/', '', $normalized) ?? '';

        return strtoupper($normalized);
    }

    public static function toLatinDigits(string $value): string
    {
        static $arabicIndic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        static $easternArabic = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        return str_replace(
            array_merge($arabicIndic, $easternArabic),
            array_merge(range('0', '9'), range('0', '9')),
            $value
        );
    }
}
