<?php

declare(strict_types=1);

namespace Validators\Core\Tests;

use PHPUnit\Framework\TestCase;
use Validators\Core\Support\Iban;
use Validators\Core\Support\Luhn;

final class SupportTest extends TestCase
{
    public function test_luhn_append_check_digit(): void
    {
        $this->assertSame('784199000000002', Luhn::appendCheckDigit('78419900000000'));
    }

    public function test_iban_generate_matches_known_valid_values(): void
    {
        $this->assertTrue(Iban::isValid(
            Iban::generate('SA', '80000000608010167519'),
            'SA'
        ));
        $this->assertSame(
            'SA0380000000608010167519',
            Iban::generate('SA', '80000000608010167519')
        );
        $this->assertSame(
            'AE070331234567890123456',
            Iban::generate('AE', '0331234567890123456')
        );
    }
}
