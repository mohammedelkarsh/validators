<?php

declare(strict_types=1);

namespace Validators\LaravelAe\Support;

use Throwable;
use Validators\Core\ValidationResult;

final class ValidationMessage
{
    public static function translate(ValidationResult $result): string
    {
        $key = $result->errorKey();

        if ($key === null) {
            return '';
        }

        if (self::canTranslate()) {
            $translated = __("validators::{$key}");

            if ($translated !== "validators::{$key}") {
                return $translated;
            }
        }

        return $result->firstError() ?? $key;
    }

    private static function canTranslate(): bool
    {
        if (! function_exists('__') || ! function_exists('app')) {
            return false;
        }

        try {
            return app()->bound('translator');
        } catch (Throwable) {
            return false;
        }
    }
}
