<?php

declare(strict_types=1);

namespace Validators\Ae\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Validators\Ae\UaeMobile;

final class UaeMobileTest extends TestCase
{
    #[DataProvider('validCases')]
    public function test_accepts_valid_numbers(string $input, string $normalized): void
    {
        $result = UaeMobile::check($input);

        $this->assertTrue($result->isValid());
        $this->assertSame($normalized, $result->normalized());
    }

    #[DataProvider('invalidCases')]
    public function test_rejects_invalid_numbers(string $input): void
    {
        $this->assertFalse(UaeMobile::isValid($input));
    }

    public function test_fake_generates_valid_numbers(): void
    {
        for ($index = 0; $index < 20; $index++) {
            $this->assertTrue(UaeMobile::isValid(UaeMobile::fake()));
        }
    }

    public static function validCases(): array
    {
        return [
            ['0501234567', '0501234567'],
            ['+971501234567', '0501234567'],
        ];
    }

    public static function invalidCases(): array
    {
        return [
            [''],
            ['0401234567'],
            ['+9710501234567'],
        ];
    }
}
