<?php

declare(strict_types=1);

namespace Validators\Sa\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Validators\Sa\SaudiIban;

final class SaudiIbanTest extends TestCase
{
    #[DataProvider('validCases')]
    public function test_accepts_valid_ibans(string $input): void
    {
        $this->assertTrue(SaudiIban::isValid($input));
    }

    #[DataProvider('invalidCases')]
    public function test_rejects_invalid_ibans(string $input): void
    {
        $this->assertFalse(SaudiIban::isValid($input));
    }

    public function test_formats_iban(): void
    {
        $this->assertSame(
            'SA03 8000 0000 6080 1016 7519',
            SaudiIban::format('SA0380000000608010167519')
        );
    }

    public function test_fake_generates_valid_ibans(): void
    {
        for ($index = 0; $index < 20; $index++) {
            $this->assertTrue(SaudiIban::isValid(SaudiIban::fake()));
        }
    }

    public static function validCases(): array
    {
        return [
            ['SA0380000000608010167519'],
            ['SA03 8000 0000 6080 1016 7519'],
        ];
    }

    public static function invalidCases(): array
    {
        return [
            [''],
            ['SA038000000060801016751'],
            ['DE02120300000000202051'],
            ['SA0380000000608010167510'],
        ];
    }
}
