<?php

declare(strict_types=1);

namespace Validators\LaravelAe\Tests;

use PHPUnit\Framework\TestCase;
use Validators\LaravelAe\Rules\EmiratesId;
use Validators\LaravelAe\Rules\UaeIban;
use Validators\LaravelAe\Rules\UaeMobile;

final class ValidationRulesTest extends TestCase
{
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
