<?php

declare(strict_types=1);

namespace Validators\Sa\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Validators\Sa\IdentityType;
use Validators\Sa\SaudiNationalId;

final class SaudiNationalIdTest extends TestCase
{
    #[DataProvider('validCases')]
    public function test_accepts_valid_ids(string $input, string $type): void
    {
        $result = SaudiNationalId::check($input);

        $this->assertTrue($result->isValid());
        $this->assertSame($type, $result->meta()['type']);
    }

    #[DataProvider('invalidCases')]
    public function test_rejects_invalid_ids(string $input): void
    {
        $this->assertFalse(SaudiNationalId::isValid($input));
    }

    public function test_type_helper(): void
    {
        $this->assertSame(IdentityType::Citizen, SaudiNationalId::type('1001244084'));
        $this->assertSame(IdentityType::Resident, SaudiNationalId::type('2001244082'));
        $this->assertNull(SaudiNationalId::type('1001244080'));
    }

    public function test_returns_error_key_with_english_fallback(): void
    {
        $result = SaudiNationalId::check('1001244080');

        $this->assertSame('sa.national_id.invalid_checksum', $result->errorKey());
        $this->assertSame('The national ID checksum is invalid.', $result->firstError());
    }

    public function test_fake_generates_valid_ids(): void
    {
        for ($index = 0; $index < 20; $index++) {
            $this->assertTrue(SaudiNationalId::isValid(SaudiNationalId::fake()));
        }

        $this->assertSame(IdentityType::Citizen, SaudiNationalId::type(SaudiNationalId::fake(IdentityType::Citizen)));
        $this->assertSame(IdentityType::Resident, SaudiNationalId::type(SaudiNationalId::fake(IdentityType::Resident)));
    }

    public static function validCases(): array
    {
        return [
            ['1001244084', 'citizen'],
            ['2001244082', 'resident'],
            ['1 001 244 084', 'citizen'],
        ];
    }

    public static function invalidCases(): array
    {
        return [
            [''],
            ['123'],
            ['3123456789'],
            ['1001244080'],
        ];
    }
}
