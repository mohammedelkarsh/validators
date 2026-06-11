<?php

declare(strict_types=1);

namespace Validators\Core\Support;

final class Luhn
{
    public static function isValid(string $digits): bool
    {
        if ($digits === '' || ! ctype_digit($digits)) {
            return false;
        }

        $sum = 0;
        $alternate = false;

        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $digit = (int) $digits[$i];

            if ($alternate) {
                $digit *= 2;

                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $alternate = ! $alternate;
        }

        return $sum % 10 === 0;
    }

    public static function appendCheckDigit(string $digits): string
    {
        if ($digits === '' || ! ctype_digit($digits)) {
            throw new \InvalidArgumentException('Luhn input must be a non-empty digit string.');
        }

        for ($checkDigit = 0; $checkDigit <= 9; $checkDigit++) {
            $candidate = $digits.$checkDigit;

            if (self::isValid($candidate)) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Unable to calculate a Luhn check digit.');
    }
}
