<?php

declare(strict_types=1);

namespace Validators\Eg\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Validators\Eg\EgyptianNationalId;
use Validators\Eg\Gender;

final class EgyptianNationalIdTest extends TestCase
{
    #[DataProvider('validCases')]
    public function test_accepts_valid_ids(string $input): void
    {
        $this->assertTrue(EgyptianNationalId::isValid($input));
    }

    #[DataProvider('invalidCases')]
    public function test_rejects_invalid_ids(string $input): void
    {
        $this->assertFalse(EgyptianNationalId::isValid($input));
    }

    public function test_extracts_metadata(): void
    {
        $result = EgyptianNationalId::check('29001011234564');

        $this->assertTrue($result->isValid());
        $this->assertSame('1990-01-01', $result->meta()['birth_date']);
        $this->assertSame(12, $result->meta()['governorate_code']);
        $this->assertSame('Dakahlia', $result->meta()['governorate']);
        $this->assertSame(Gender::Female->value, $result->meta()['gender']);
    }

    public function test_calculates_checksum(): void
    {
        $this->assertSame(4, EgyptianNationalId::calculateCheckDigit('2900101123456'));
    }

    public function test_fake_generates_valid_ids(): void
    {
        for ($index = 0; $index < 20; $index++) {
            $this->assertTrue(EgyptianNationalId::isValid(EgyptianNationalId::fake()));
        }
    }

    public static function validCases(): array
    {
        return [
            ['29001011234564'],
            ['2 900 101 123 456 4'],
        ];
    }

    public static function invalidCases(): array
    {
        return [
            [''],
            ['123'],
            ['10201011234567'],
            ['30213011234567'],
            ['29001011234560'],
        ];
    }
}
