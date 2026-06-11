<?php

declare(strict_types=1);

namespace Validators\Core\Tests;

use PHPUnit\Framework\TestCase;
use Validators\Core\ValidationResult;

final class ValidationResultTest extends TestCase
{
    public function test_exposes_error_key_and_english_fallback(): void
    {
        $result = ValidationResult::invalid('123', 'sa.national_id.invalid_checksum');

        $this->assertFalse($result->isValid());
        $this->assertSame('sa.national_id.invalid_checksum', $result->errorKey());
        $this->assertSame('The national ID checksum is invalid.', $result->firstError());
        $this->assertSame(['The national ID checksum is invalid.'], $result->errors());
    }
}
