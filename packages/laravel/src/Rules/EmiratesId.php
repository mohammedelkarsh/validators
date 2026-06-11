<?php

declare(strict_types=1);

namespace Validators\Laravel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Validators\Ae\EmiratesId as EmiratesIdValidator;
use Validators\Laravel\Support\ValidationMessage;

final class EmiratesId implements ValidationRule
{
    public function __construct(private readonly bool $strict = true) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $result = EmiratesIdValidator::check($value, $this->strict);

        if ($result->isValid()) {
            return;
        }

        $fail(ValidationMessage::translate($result));
    }
}
