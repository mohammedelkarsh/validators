<?php

declare(strict_types=1);

namespace Validators\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Validators\Eg\EgyptianNationalId as EgyptianNationalIdValidator;
use Validators\Laravel\Support\ValidationMessage;

final class EgyptianNationalId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = EgyptianNationalIdValidator::check($value);

        if ($result->isValid()) {
            return;
        }

        $fail(ValidationMessage::translate($result));
    }
}
