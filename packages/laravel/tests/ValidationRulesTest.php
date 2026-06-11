<?php

declare(strict_types=1);

namespace Validators\Laravel\Tests;

use PHPUnit\Framework\TestCase;
use Validators\Laravel\Rules\EgyptianNationalId;
use Validators\Laravel\Rules\EmiratesId;
use Validators\Laravel\Rules\SaudiIban;
use Validators\Laravel\Rules\SaudiMobile;
use Validators\Laravel\Rules\SaudiNationalId;
use Validators\Laravel\Rules\UaeIban;
use Validators\Laravel\Rules\UaeMobile;

final class ValidationRulesTest extends TestCase
{
    public function test_saudi_national_id_rule(): void
    {
        $this->assertRulePasses(new SaudiNationalId(), '1001244084');
        $this->assertRuleFails(new SaudiNationalId(), '1001244080', 'The national ID checksum is invalid.');
    }

    public function test_saudi_mobile_rule(): void
    {
        $this->assertRulePasses(new SaudiMobile(), '0501234567');
        $this->assertRuleFails(new SaudiMobile(), '0401234567', 'The mobile number must be a valid Saudi number (05XXXXXXXX).');
    }

    public function test_saudi_iban_rule(): void
    {
        $this->assertRulePasses(new SaudiIban(), 'SA0380000000608010167519');
        $this->assertRuleFails(new SaudiIban(), 'DE02120300000000202051', 'The IBAN must start with SA.');
    }

    public function test_egyptian_national_id_rule(): void
    {
        $this->assertRulePasses(new EgyptianNationalId(), '29001011234564');
        $this->assertRuleFails(new EgyptianNationalId(), '29001011234560', 'The national ID checksum is invalid.');
    }

    public function test_emirates_id_rule(): void
    {
        $this->assertRulePasses(new EmiratesId(), '784199000000002');
        $this->assertRuleFails(new EmiratesId(), '784199000000001', 'The Emirates ID checksum is invalid.');
    }

    public function test_uae_mobile_rule(): void
    {
        $this->assertRulePasses(new UaeMobile(), '0501234567');
        $this->assertRuleFails(new UaeMobile(), '0401234567', 'The mobile number must be a valid UAE number (05XXXXXXXX).');
    }

    public function test_uae_iban_rule(): void
    {
        $this->assertRulePasses(new UaeIban(), 'AE070331234567890123456');
        $this->assertRuleFails(new UaeIban(), 'SA0380000000608010167519', 'The IBAN must start with AE.');
    }

    private function assertRulePasses(object $rule, mixed $value): void
    {
        $failed = null;

        $rule->validate('field', $value, function (string $message) use (&$failed): void {
            $failed = $message;
        });

        $this->assertNull($failed);
    }

    private function assertRuleFails(object $rule, mixed $value, string $expectedMessage): void
    {
        $failed = null;

        $rule->validate('field', $value, function (string $message) use (&$failed): void {
            $failed = $message;
        });

        $this->assertSame($expectedMessage, $failed);
    }
}
