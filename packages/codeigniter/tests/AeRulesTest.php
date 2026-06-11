<?php

declare(strict_types=1);

namespace Validators\CodeIgniter\Tests;

use PHPUnit\Framework\TestCase;
use Validators\CodeIgniter\AeRules;

final class AeRulesTest extends TestCase
{
    public function test_emirates_id_rule(): void
    {
        $rules = new AeRules();
        $error = null;

        $this->assertTrue($rules->emirates_id('784199000000002', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->emirates_id('784199000000001', $error));
        $this->assertSame('ae.emirates_id.invalid_checksum', $error);
    }

    public function test_uae_mobile_rule(): void
    {
        $rules = new AeRules();
        $error = null;

        $this->assertTrue($rules->uae_mobile('0501234567', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->uae_mobile('0401234567', $error));
        $this->assertSame('ae.mobile.invalid_format', $error);
    }

    public function test_uae_iban_rule(): void
    {
        $rules = new AeRules();
        $error = null;

        $this->assertTrue($rules->uae_iban('AE070331234567890123456', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->uae_iban('SA0380000000608010167519', $error));
        $this->assertSame('ae.iban.invalid_country', $error);
    }
}
