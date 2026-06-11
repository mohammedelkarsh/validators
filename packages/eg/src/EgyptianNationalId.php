<?php

declare(strict_types=1);

namespace Validators\Eg;

use Validators\Core\Normalizer;
use Validators\Core\ValidationResult;

final class EgyptianNationalId
{
    /** @var list<int> */
    private const CHECKSUM_WEIGHTS = [2, 7, 6, 5, 4, 3, 2, 7, 6, 5, 4, 3, 2];

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
        $century = random_int(2, 3);
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');
        $currentDay = (int) date('d');
        $fullYear = $century === 2
            ? random_int(1950, 1999)
            : random_int(2000, $currentYear);

        if ($fullYear === $currentYear) {
            $month = random_int(1, $currentMonth);
            $maxDay = $month === $currentMonth
                ? $currentDay
                : (int) date('t', mktime(0, 0, 0, $month, 1, $fullYear));
            $day = random_int(1, max(1, $maxDay));
        } else {
            $month = random_int(1, 12);
            $day = random_int(1, (int) date('t', mktime(0, 0, 0, $month, 1, $fullYear)));
        }

        $governorateCodes = Governorates::codes();
        $governorate = $governorateCodes[random_int(0, count($governorateCodes) - 1)];
        $serial = random_int(0, 999);
        $gender = random_int(1, 9);

        $firstThirteen = sprintf(
            '%d%02d%02d%02d%02d%03d%d',
            $century,
            $fullYear % 100,
            $month,
            $day,
            $governorate,
            $serial,
            $gender
        );

        return $firstThirteen.self::calculateCheckDigit($firstThirteen);
    }

    public function validate(mixed $value): ValidationResult
    {
        $normalized = Normalizer::digitsOnly($value);

        if ($normalized === '') {
            return ValidationResult::invalid('', 'eg.national_id.required');
        }

        if (strlen($normalized) !== 14) {
            return ValidationResult::invalid($normalized, 'eg.national_id.invalid_length');
        }

        $centuryDigit = (int) $normalized[0];

        if (! in_array($centuryDigit, [2, 3], true)) {
            return ValidationResult::invalid($normalized, 'eg.national_id.invalid_century');
        }

        $year = (int) substr($normalized, 1, 2);
        $month = (int) substr($normalized, 3, 2);
        $day = (int) substr($normalized, 5, 2);
        $fullYear = ($centuryDigit === 2 ? 1900 : 2000) + $year;

        if ($month < 1 || $month > 12) {
            return ValidationResult::invalid($normalized, 'eg.national_id.invalid_month');
        }

        if (! checkdate($month, $day, $fullYear)) {
            return ValidationResult::invalid($normalized, 'eg.national_id.invalid_date');
        }

        $birthDate = sprintf('%04d-%02d-%02d', $fullYear, $month, $day);

        if ($birthDate > date('Y-m-d')) {
            return ValidationResult::invalid($normalized, 'eg.national_id.future_birth_date');
        }

        $governorateCode = (int) substr($normalized, 7, 2);

        if (! Governorates::isValid($governorateCode)) {
            return ValidationResult::invalid($normalized, 'eg.national_id.invalid_governorate');
        }

        $expectedCheckDigit = self::calculateCheckDigit(substr($normalized, 0, 13));
        $actualCheckDigit = (int) $normalized[13];

        if ($actualCheckDigit !== $expectedCheckDigit) {
            return ValidationResult::invalid($normalized, 'eg.national_id.invalid_checksum');
        }

        $genderDigit = (int) $normalized[12];

        return ValidationResult::valid($normalized, [
            'birth_date' => $birthDate,
            'governorate_code' => $governorateCode,
            'governorate' => Governorates::name($governorateCode),
            'gender' => $genderDigit % 2 === 1 ? Gender::Male->value : Gender::Female->value,
            'century' => $centuryDigit === 2 ? '1900-1999' : '2000-2099',
        ]);
    }

    public static function calculateCheckDigit(string $firstThirteenDigits): int
    {
        $sum = 0;

        for ($index = 0; $index < 13; $index++) {
            $sum += ((int) $firstThirteenDigits[$index]) * self::CHECKSUM_WEIGHTS[$index];
        }

        return abs(11 - ($sum % 11)) % 10;
    }
}
