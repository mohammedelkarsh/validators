<?php

declare(strict_types=1);

namespace Validators\CodeIgniter\Tests;

use PHPUnit\Framework\TestCase;
use Validators\CodeIgniter\SaRules;

final class SaRulesTest extends TestCase
{
    public function test_saudi_national_id_rule(): void
    {
        $rules = new SaRules();
        $error = null;

        $this->assertTrue($rules->saudi_national_id('1001244084', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->saudi_national_id('1001244080', $error));
        $this->assertSame('sa.national_id.invalid_checksum', $error);
    }

    public function test_saudi_mobile_rule(): void
    {
        $rules = new SaRules();
        $error = null;

        $this->assertTrue($rules->saudi_mobile('0501234567', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->saudi_mobile('0401234567', $error));
        $this->assertSame('sa.mobile.invalid_format', $error);
    }

    public function test_saudi_iban_rule(): void
    {
        $rules = new SaRules();
        $error = null;

        $this->assertTrue($rules->saudi_iban('SA0380000000608010167519', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->saudi_iban('DE02120300000000202051', $error));
        $this->assertSame('sa.iban.invalid_country', $error);
    }
}
