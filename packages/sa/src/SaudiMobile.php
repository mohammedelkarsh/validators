<?php

declare(strict_types=1);

namespace Validators\Sa;

use Validators\Core\Normalizer;
use Validators\Core\ValidationResult;

final class SaudiMobile
{
    private const LOCAL_PATTERN = '/^05\d{8}$/';

    public static function check(mixed $value): ValidationResult
    {
        return (new self())->validate($value);
    }

    public static function isValid(mixed $value): bool
    {
        return self::check($value)->isValid();
    }

    public static function fake(): string
    {
        return '05'.str_pad((string) random_int(0, 99_999_999), 8, '0', STR_PAD_LEFT);
    }

    public function validate(mixed $value): ValidationResult
    {
        $normalized = self::normalize($value);

        if ($normalized === '') {
            return ValidationResult::invalid('', 'sa.mobile.required');
        }

        if (! preg_match(self::LOCAL_PATTERN, $normalized)) {
            return ValidationResult::invalid($normalized, 'sa.mobile.invalid_format');
        }

        return ValidationResult::valid($normalized, [
            'international' => '+966'.substr($normalized, 1),
        ]);
    }

    public static function normalize(mixed $value): string
    {
        $input = trim(Normalizer::toLatinDigits((string) $value));
        $input = preg_replace('/[\s\-()]+/', '', $input) ?? '';

        if (str_starts_with($input, '+966')) {
            $input = '0'.substr($input, 4);
        } elseif (str_starts_with($input, '966')) {
            $input = '0'.substr($input, 3);
        } elseif (str_starts_with($input, '5') && strlen($input) === 9) {
            $input = '0'.$input;
        }

        return Normalizer::digitsOnly($input);
    }
}
