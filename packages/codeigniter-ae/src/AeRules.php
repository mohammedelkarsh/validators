<?php

declare(strict_types=1);

namespace Validators\CodeIgniterAe;

use Validators\Ae\EmiratesId;
use Validators\Ae\UaeIban;
use Validators\Ae\UaeMobile;

final class AeRules
{
    public function emirates_id(?string $value, ?string &$error = null): bool
    {
        $result = EmiratesId::check($value ?? '');

        if ($result->isValid()) {
            return true;
        }

        $error = $result->errorKey();

        return false;
    }

    public function uae_mobile(?string $value, ?string &$error = null): bool
    {
        $result = UaeMobile::check($value ?? '');

        if ($result->isValid()) {
            return true;
        }

        $error = $result->errorKey();

        return false;
    }

    public function uae_iban(?string $value, ?string &$error = null): bool
    {
        $result = UaeIban::check($value ?? '');

        if ($result->isValid()) {
            return true;
        }

        $error = $result->errorKey();

        return false;
    }
}
