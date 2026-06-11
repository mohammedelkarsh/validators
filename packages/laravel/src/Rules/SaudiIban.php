<?php

declare(strict_types=1);

namespace Validators\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Validators\Laravel\Support\ValidationMessage;
use Validators\Sa\SaudiIban as SaudiIbanValidator;

final class SaudiIban implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = SaudiIbanValidator::check($value);

        if ($result->isValid()) {
            return;
        }

        $fail(ValidationMessage::translate($result));
    }
}
