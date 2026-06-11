<?php

declare(strict_types=1);

namespace Validators\CodeIgniterEg;

use Validators\Eg\EgyptianNationalId;

final class EgRules
{
    public function egyptian_national_id(?string $value, ?string &$error = null): bool
    {
        $result = EgyptianNationalId::check($value ?? '');

        if ($result->isValid()) {
            return true;
        }

        $error = $result->errorKey();

        return false;
    }
}
