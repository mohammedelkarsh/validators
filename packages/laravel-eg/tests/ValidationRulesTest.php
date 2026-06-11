<?php

declare(strict_types=1);

namespace Validators\LaravelEg\Tests;

use PHPUnit\Framework\TestCase;
use Validators\LaravelEg\Rules\EgyptianNationalId;

final class ValidationRulesTest extends TestCase
{
    public function test_egyptian_national_id_rule(): void
    {
        $failed = null;

        (new EgyptianNationalId())->validate('field', '29001011234564', function (string $message) use (&$failed): void {
            $failed = $message;
        });

        $this->assertNull($failed);

        (new EgyptianNationalId())->validate('field', '29001011234560', function (string $message) use (&$failed): void {
            $failed = $message;
        });

        $this->assertSame('The national ID checksum is invalid.', $failed);
    }
}
