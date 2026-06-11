<?php

declare(strict_types=1);

namespace Validators\Core\Tests;

use PHPUnit\Framework\TestCase;
use Validators\Core\Normalizer;

final class NormalizerTest extends TestCase
{
    public function test_converts_arabic_indic_digits(): void
    {
        $this->assertSame('0501234567', Normalizer::digitsOnly('٠٥٠١٢٣٤٥٦٧'));
    }

    public function test_strips_non_digits(): void
    {
        $this->assertSame('1001244084', Normalizer::digitsOnly('1 001-244-084'));
    }

    public function test_rejects_non_scalar_values(): void
    {
        $this->assertSame('', Normalizer::digitsOnly(new \stdClass()));
        $this->assertSame('', Normalizer::digitsOnly(['1', '2']));
    }
}
