<?php

declare(strict_types=1);

namespace Validators\Core;

final class ErrorMessages
{
    /** @var array<string, string> */
    private const MESSAGES = [
        // Saudi — national ID
        'sa.national_id.required' => 'The national ID is required.',
        'sa.national_id.invalid_length' => 'The national ID must be exactly 10 digits.',
        'sa.national_id.invalid_prefix' => 'The national ID must start with 1 (citizen) or 2 (resident).',
        'sa.national_id.invalid_checksum' => 'The national ID checksum is invalid.',
        'sa.national_id.invalid' => 'The national ID must be a valid Saudi national ID or iqama number.',

        // Saudi — mobile
        'sa.mobile.required' => 'The mobile number is required.',
        'sa.mobile.invalid_format' => 'The mobile number must be a valid Saudi number (05XXXXXXXX).',
        'sa.mobile.invalid' => 'The mobile number must be a valid Saudi mobile number.',

        // Saudi — IBAN
        'sa.iban.required' => 'The IBAN is required.',
        'sa.iban.invalid_country' => 'The IBAN must start with SA.',
        'sa.iban.invalid_length' => 'The Saudi IBAN must be exactly 24 characters.',
        'sa.iban.invalid_characters' => 'The IBAN contains invalid characters.',
        'sa.iban.invalid_checksum' => 'The IBAN checksum is invalid.',
        'sa.iban.invalid' => 'The IBAN must be a valid Saudi IBAN.',

        // Egypt — national ID
        'eg.national_id.required' => 'The national ID is required.',
        'eg.national_id.invalid_length' => 'The national ID must be exactly 14 digits.',
        'eg.national_id.invalid_century' => 'The national ID century digit must be 2 or 3.',
        'eg.national_id.invalid_month' => 'The national ID birth month is invalid.',
        'eg.national_id.invalid_date' => 'The national ID birth date is invalid.',
        'eg.national_id.future_birth_date' => 'The national ID birth date cannot be in the future.',
        'eg.national_id.invalid_governorate' => 'The national ID governorate code is invalid.',
        'eg.national_id.invalid_checksum' => 'The national ID checksum is invalid.',
        'eg.national_id.invalid' => 'The national ID must be a valid Egyptian national ID.',

        // UAE — Emirates ID
        'ae.emirates_id.required' => 'The Emirates ID is required.',
        'ae.emirates_id.invalid_length' => 'The Emirates ID must be exactly 15 digits.',
        'ae.emirates_id.invalid_prefix' => 'The Emirates ID must start with 784.',
        'ae.emirates_id.invalid_checksum' => 'The Emirates ID checksum is invalid.',
        'ae.emirates_id.invalid' => 'The Emirates ID must be a valid Emirates ID.',

        // UAE — mobile
        'ae.mobile.required' => 'The mobile number is required.',
        'ae.mobile.invalid_format' => 'The mobile number must be a valid UAE number (05XXXXXXXX).',
        'ae.mobile.invalid' => 'The mobile number must be a valid UAE mobile number.',

        // UAE — IBAN
        'ae.iban.required' => 'The IBAN is required.',
        'ae.iban.invalid_country' => 'The IBAN must start with AE.',
        'ae.iban.invalid_length' => 'The UAE IBAN must be exactly 23 characters.',
        'ae.iban.invalid_characters' => 'The IBAN contains invalid characters.',
        'ae.iban.invalid_checksum' => 'The IBAN checksum is invalid.',
        'ae.iban.invalid' => 'The IBAN must be a valid UAE IBAN.',
    ];

    public static function message(string $key): string
    {
        return self::MESSAGES[$key] ?? $key;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::MESSAGES);
    }

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return self::MESSAGES;
    }
}
