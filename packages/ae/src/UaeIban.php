<?php

declare(strict_types=1);

namespace Validators\Ae;

use Validators\Core\Normalizer;
use Validators\Core\Support\Iban as IbanSupport;
use Validators\Core\ValidationResult;

final class UaeIban
{
    private const LENGTH = 23;

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
        $bban = '';

        for ($index = 0; $index < 19; $index++) {
            $bban .= (string) random_int(0, 9);
        }

        return IbanSupport::generate('AE', $bban);
    }

    public function validate(mixed $value): ValidationResult
    {
        $normalized = Normalizer::alphanumericUpper($value);

        if ($normalized === '') {
            return ValidationResult::invalid('', 'ae.iban.required');
        }

        if (! str_starts_with($normalized, 'AE')) {
            return ValidationResult::invalid($normalized, 'ae.iban.invalid_country');
        }

        if (strlen($normalized) !== self::LENGTH) {
            return ValidationResult::invalid($normalized, 'ae.iban.invalid_length');
        }

        if (! ctype_alnum($normalized)) {
            return ValidationResult::invalid($normalized, 'ae.iban.invalid_characters');
        }

        if (! IbanSupport::isValid($normalized, 'AE')) {
            return ValidationResult::invalid($normalized, 'ae.iban.invalid_checksum');
        }

        return ValidationResult::valid($normalized, [
            'formatted' => self::format($normalized),
            'bank_code' => substr($normalized, 4, 3),
        ]);
    }

    public static function format(string $iban): string
    {
        $iban = Normalizer::alphanumericUpper($iban);

        return trim(chunk_split($iban, 4, ' '));
    }
}
