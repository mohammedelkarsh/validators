<?php

declare(strict_types=1);

namespace Validators\LaravelAe\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Validators\Ae\EmiratesId as EmiratesIdValidator;
use Validators\LaravelAe\Support\ValidationMessage;

final class EmiratesId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = EmiratesIdValidator::check($value);

        if ($result->isValid()) {
            return;
        }

        $fail(ValidationMessage::translate($result));
    }
}
