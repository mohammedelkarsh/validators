<?php

declare(strict_types=1);

namespace Validators\LaravelAe\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Validators\Ae\UaeMobile as UaeMobileValidator;
use Validators\LaravelAe\Support\ValidationMessage;

final class UaeMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = UaeMobileValidator::check($value);

        if ($result->isValid()) {
            return;
        }

        $fail(ValidationMessage::translate($result));
    }
}
