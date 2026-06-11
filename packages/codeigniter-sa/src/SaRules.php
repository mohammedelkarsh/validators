<?php

declare(strict_types=1);

namespace Validators\CodeIgniterSa;

use Validators\Sa\SaudiIban;
use Validators\Sa\SaudiMobile;
use Validators\Sa\SaudiNationalId;

final class SaRules
{
    public function saudi_national_id(?string $value, ?string &$error = null): bool
    {
        $result = SaudiNationalId::check($value ?? '');

        if ($result->isValid()) {
            return true;
        }

        $error = $result->errorKey();

        return false;
    }

    public function saudi_mobile(?string $value, ?string &$error = null): bool
    {
        $result = SaudiMobile::check($value ?? '');

        if ($result->isValid()) {
            return true;
        }

        $error = $result->errorKey();

        return false;
    }

    public function saudi_iban(?string $value, ?string &$error = null): bool
    {
        $result = SaudiIban::check($value ?? '');

        if ($result->isValid()) {
            return true;
        }

        $error = $result->errorKey();

        return false;
    }
}
