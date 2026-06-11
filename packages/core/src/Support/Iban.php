<?php

declare(strict_types=1);

namespace Validators\Core\Support;

final class Iban
{
    public static function isValid(string $iban, ?string $countryCode = null): bool
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $iban) ?? '');

        if ($iban === '' || ! preg_match('/^[A-Z0-9]+$/', $iban)) {
            return false;
        }

        if ($countryCode !== null && ! str_starts_with($iban, strtoupper($countryCode))) {
            return false;
        }

        $rearranged = substr($iban, 4).substr($iban, 0, 4);
        $numeric = '';

        foreach (str_split($rearranged) as $character) {
            $numeric .= ctype_alpha($character)
                ? (string) (ord($character) - 55)
                : $character;
        }

        $remainder = 0;

        foreach (str_split($numeric) as $digit) {
            $remainder = ($remainder * 10 + (int) $digit) % 97;
        }

        return $remainder === 1;
    }

    public static function generate(string $countryCode, string $bban): string
    {
        $countryCode = strtoupper($countryCode);
        $bban = strtoupper($bban);

        if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new \InvalidArgumentException('Country code must be two letters.');
        }

        if ($bban === '' || ! preg_match('/^[A-Z0-9]+$/', $bban)) {
            throw new \InvalidArgumentException('BBAN must be a non-empty alphanumeric string.');
        }

        $rearranged = $bban.$countryCode.'00';
        $numeric = '';

        foreach (str_split($rearranged) as $character) {
            $numeric .= ctype_alpha($character)
                ? (string) (ord($character) - 55)
                : $character;
        }

        $remainder = 0;

        foreach (str_split($numeric) as $digit) {
            $remainder = ($remainder * 10 + (int) $digit) % 97;
        }

        $checkDigits = str_pad((string) (98 - $remainder), 2, '0', STR_PAD_LEFT);

        return $countryCode.$checkDigits.$bban;
    }
}
