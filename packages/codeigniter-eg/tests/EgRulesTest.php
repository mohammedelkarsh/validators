<?php

declare(strict_types=1);

namespace Validators\CodeIgniterEg\Tests;

use PHPUnit\Framework\TestCase;
use Validators\CodeIgniterEg\EgRules;

final class EgRulesTest extends TestCase
{
    public function test_egyptian_national_id_rule(): void
    {
        $rules = new EgRules();
        $error = null;

        $this->assertTrue($rules->egyptian_national_id('29001011234564', $error));
        $this->assertNull($error);

        $this->assertFalse($rules->egyptian_national_id('29001011234560', $error));
        $this->assertSame('eg.national_id.invalid_checksum', $error);

        $this->assertFalse($rules->egyptian_national_id(null, $error));
        $this->assertSame('eg.national_id.required', $error);
    }
}
