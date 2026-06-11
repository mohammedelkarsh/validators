<?php

declare(strict_types=1);

namespace Validators\Ae\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Validators\Ae\EmiratesId;

final class EmiratesIdTest extends TestCase
{
    #[DataProvider('validCases')]
    public function test_accepts_valid_ids(string $input): void
    {
        $this->assertTrue(EmiratesId::isValid($input));
    }

    #[DataProvider('invalidCases')]
    public function test_rejects_invalid_ids(string $input): void
    {
        $this->assertFalse(EmiratesId::isValid($input));
    }

    public function test_lenient_mode_accepts_format_without_luhn(): void
    {
        $this->assertTrue(EmiratesId::isValid('784199000000001', strict: false));
    }

    public function test_formats_id(): void
    {
        $this->assertSame('784-1990-0000000-2', EmiratesId::format('784199000000002'));
    }

    public function test_fake_generates_valid_ids(): void
    {
        for ($index = 0; $index < 20; $index++) {
            $this->assertTrue(EmiratesId::isValid(EmiratesId::fake()));
        }
    }

    public static function validCases(): array
    {
        return [
            ['784199000000002'],
            ['784-1990-0000000-2'],
        ];
    }

    public static function invalidCases(): array
    {
        return [
            [''],
            ['885199000000002'],
            ['784199000000001'],
        ];
    }
}
