<?php

declare(strict_types=1);

namespace Validators\LaravelSa\Tests;

use PHPUnit\Framework\TestCase;
use Validators\LaravelSa\Rules\SaudiIban;
use Validators\LaravelSa\Rules\SaudiMobile;
use Validators\LaravelSa\Rules\SaudiNationalId;

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
