<?php

declare(strict_types=1);

namespace Validators\Ae;

use Validators\Core\Normalizer;
use Validators\Core\Support\Luhn;
use Validators\Core\ValidationResult;

final class EmiratesId
{
    private const LENGTH = 15;

    private const PREFIX = '784';

    public static function check(mixed $value, bool $strict = true): ValidationResult
    {
        return (new self())->validate($value, $strict);
    }

    public static function isValid(mixed $value, bool $strict = true): bool
    {
        return self::check($value, $strict)->isValid();
    }

    public static function fake(bool $strict = true): string
    {
        $digits = self::PREFIX.(string) random_int(1960, (int) date('Y'));

        for ($index = 0; $index < 7; $index++) {
            $digits .= (string) random_int(0, 9);
        }

        if ($strict) {
            return Luhn::appendCheckDigit($digits);
        }

        return $digits.(string) random_int(0, 9);
    }

    public function validate(mixed $value, bool $strict = true): ValidationResult
    {
        $normalized = Normalizer::digitsOnly($value);

        if ($normalized === '') {
            return ValidationResult::invalid('', 'ae.emirates_id.required');
        }

        if (strlen($normalized) !== self::LENGTH) {
            return ValidationResult::invalid($normalized, 'ae.emirates_id.invalid_length');
        }

        if (! str_starts_with($normalized, self::PREFIX)) {
            return ValidationResult::invalid($normalized, 'ae.emirates_id.invalid_prefix');
        }

        if ($strict && ! Luhn::isValid($normalized)) {
            return ValidationResult::invalid($normalized, 'ae.emirates_id.invalid_checksum');
        }

        return ValidationResult::valid($normalized, [
            'formatted' => self::format($normalized),
            'strict' => $strict,
            'registration_year' => substr($normalized, 3, 4),
        ]);
    }

    public static function format(string $digits): string
    {
        $digits = Normalizer::digitsOnly($digits);

        if (strlen($digits) !== self::LENGTH) {
            return $digits;
        }

        return substr($digits, 0, 3).'-'
            .substr($digits, 3, 4).'-'
            .substr($digits, 7, 7).'-'
            .substr($digits, 14, 1);
    }
}
