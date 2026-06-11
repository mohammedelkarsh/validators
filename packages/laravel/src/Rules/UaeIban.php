<?php

declare(strict_types=1);

namespace Validators\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Validators\Ae\UaeIban as UaeIbanValidator;
use Validators\Laravel\Support\ValidationMessage;

final class UaeIban implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = UaeIbanValidator::check($value);

        if ($result->isValid()) {
            return;
        }

        $fail(ValidationMessage::translate($result));
    }
}
