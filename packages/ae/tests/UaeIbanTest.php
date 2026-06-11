<?php

declare(strict_types=1);

namespace Validators\Ae\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Validators\Ae\UaeIban;

final class UaeIbanTest extends TestCase
{
    #[DataProvider('validCases')]
    public function test_accepts_valid_ibans(string $input): void
    {
        $this->assertTrue(UaeIban::isValid($input));
    }

    #[DataProvider('invalidCases')]
    public function test_rejects_invalid_ibans(string $input): void
    {
        $this->assertFalse(UaeIban::isValid($input));
    }

    public function test_fake_generates_valid_ibans(): void
    {
        for ($index = 0; $index < 20; $index++) {
            $this->assertTrue(UaeIban::isValid(UaeIban::fake()));
        }
    }

    public static function validCases(): array
    {
        return [
            ['AE070331234567890123456'],
            ['AE07 0331 2345 6789 0123 456'],
        ];
    }

    public static function invalidCases(): array
    {
        return [
            [''],
            ['SA0380000000608010167519'],
            ['AE070331234567890123450'],
        ];
    }
}
