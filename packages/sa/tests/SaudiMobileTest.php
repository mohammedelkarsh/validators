<?php

declare(strict_types=1);

namespace Validators\Sa\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Validators\Sa\SaudiMobile;

final class SaudiMobileTest extends TestCase
{
    #[DataProvider('validCases')]
    public function test_accepts_valid_numbers(string $input, string $normalized): void
    {
        $result = SaudiMobile::check($input);

        $this->assertTrue($result->isValid());
        $this->assertSame($normalized, $result->normalized());
        $this->assertSame('+966'.substr($normalized, 1), $result->meta()['international']);
    }

    #[DataProvider('invalidCases')]
    public function test_rejects_invalid_numbers(string $input): void
    {
        $this->assertFalse(SaudiMobile::isValid($input));
    }

    public function test_fake_generates_valid_numbers(): void
    {
        for ($index = 0; $index < 20; $index++) {
            $this->assertTrue(SaudiMobile::isValid(SaudiMobile::fake()));
        }
    }

    public static function validCases(): array
    {
        return [
            ['0501234567', '0501234567'],
            ['+966501234567', '0501234567'],
            ['٠٥٠١٢٣٤٥٦٧', '0501234567'],
        ];
    }

    public static function invalidCases(): array
    {
        return [
            [''],
            ['0401234567'],
            ['050123456'],
            ['1501234567'],
        ];
    }
}
