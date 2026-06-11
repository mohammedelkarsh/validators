<?php

declare(strict_types=1);

namespace Validators\LaravelSa\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Validators\LaravelSa\Support\ValidationMessage;
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
