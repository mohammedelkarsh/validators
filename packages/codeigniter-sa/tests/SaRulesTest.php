<?php

declare(strict_types=1);

namespace Validators\CodeIgniterSa\Tests;

use PHPUnit\Framework\TestCase;
use Validators\CodeIgniterSa\SaRules;

final class SaRulesTest extends TestCase
{
    public function test_saudi_rules(): void
    {
        $rules = new SaRules();
        $error = null;

        $this->assertTrue($rules->saudi_national_id('1001244084', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->saudi_national_id('1001244080', $error));
        $this->assertSame('sa.national_id.invalid_checksum', $error);

        $this->assertTrue($rules->saudi_mobile('0501234567', $error));
        $this->assertFalse($rules->saudi_mobile('0401234567', $error));
        $this->assertSame('sa.mobile.invalid_format', $error);

        $this->assertTrue($rules->saudi_iban('SA0380000000608010167519', $error));
        $this->assertFalse($rules->saudi_iban('DE02120300000000202051', $error));
        $this->assertSame('sa.iban.invalid_country', $error);
    }
}
