<?php

declare(strict_types=1);

namespace Validators\Sa;

use Validators\Core\Normalizer;
use Validators\Core\ValidationResult;

final class SaudiNationalId
{
    public static function check(mixed $value): ValidationResult
    {
        return (new self())->validate($value);
    }

    public static function isValid(mixed $value): bool
    {
        return self::check($value)->isValid();
    }

    public static function fake(?IdentityType $type = null): string
    {
        $prefix = match ($type) {
            IdentityType::Citizen => '1',
            IdentityType::Resident => '2',
            null => (string) random_int(1, 2),
        };

        $digits = $prefix;

        for ($index = 0; $index < 8; $index++) {
            $digits .= (string) random_int(0, 9);
        }

        return self::withCheckDigit($digits);
    }

    public function validate(mixed $value): ValidationResult
    {
        $normalized = Normalizer::digitsOnly($value);

        if ($normalized === '') {
            return ValidationResult::invalid('', 'sa.national_id.required');
        }

        if (strlen($normalized) !== 10) {
            return ValidationResult::invalid($normalized, 'sa.national_id.invalid_length');
        }

        $typeDigit = $normalized[0];

        if (! in_array($typeDigit, ['1', '2'], true)) {
            return ValidationResult::invalid($normalized, 'sa.national_id.invalid_prefix');
        }

        if (! self::passesChecksum($normalized)) {
            return ValidationResult::invalid($normalized, 'sa.national_id.invalid_checksum');
        }

        return ValidationResult::valid($normalized, [
            'type' => $typeDigit === '1' ? IdentityType::Citizen->value : IdentityType::Resident->value,
        ]);
    }

    public static function type(mixed $value): ?IdentityType
    {
        $result = (new self())->validate($value);

        if (! $result->isValid()) {
            return null;
        }

        return $result->meta()['type'] === IdentityType::Citizen->value
            ? IdentityType::Citizen
            : IdentityType::Resident;
    }

    public static function passesChecksum(string $digits): bool
    {
        if (strlen($digits) !== 10 || ! ctype_digit($digits)) {
            return false;
        }

        $sum = 0;

        for ($index = 0; $index < 10; $index++) {
            $digit = (int) $digits[$index];

            if ($index % 2 === 0) {
                $doubled = str_pad((string) ($digit * 2), 2, '0', STR_PAD_LEFT);
                $sum += (int) $doubled[0] + (int) $doubled[1];
            } else {
                $sum += $digit;
            }
        }

        return $sum % 10 === 0;
    }

    private static function withCheckDigit(string $firstNineDigits): string
    {
        for ($checkDigit = 0; $checkDigit <= 9; $checkDigit++) {
            $candidate = $firstNineDigits.$checkDigit;

            if (self::passesChecksum($candidate)) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Unable to calculate a Saudi national ID check digit.');
    }
}
