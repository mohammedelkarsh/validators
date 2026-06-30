<?php

declare(strict_types=1);

namespace Validators\LaravelAe\Tests;

use PHPUnit\Framework\TestCase;

final class TranslationFilesTest extends TestCase
{
    public function test_arabic_translation_keys_match_english(): void
    {
        $root = dirname(__DIR__);
        $en = require $root.'/lang/en/validation.php';
        $ar = require $root.'/lang/ar/validation.php';

        $this->assertSame(array_keys($en), array_keys($ar));

        foreach ($ar as $message) {
            $this->assertNotSame('', trim($message));
        }
    }
}
