<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

use Validators\Ae\EmiratesId;
use Validators\Ae\UaeIban;
use Validators\Ae\UaeMobile;
use Validators\Eg\EgyptianNationalId;
use Validators\Sa\IdentityType;
use Validators\Sa\SaudiIban;
use Validators\Sa\SaudiMobile;
use Validators\Sa\SaudiNationalId;

/** @var list<array{0: string, 1: callable(): bool}> $checks */
$checks = [
    ['SA citizen id', fn (): bool => SaudiNationalId::isValid('1001244084')],
    ['SA resident type', fn (): bool => SaudiNationalId::type('2001244082') === IdentityType::Resident],
    ['SA invalid id rejected', fn (): bool => ! SaudiNationalId::isValid('1001244080')],
    ['SA local mobile', fn (): bool => SaudiMobile::isValid('0501234567')],
    ['SA international mobile', fn (): bool => SaudiMobile::isValid('+966501234567')],
    ['SA iban', fn (): bool => SaudiIban::isValid('SA0380000000608010167519')],
    ['EG national id', fn (): bool => EgyptianNationalId::isValid('29001011234564')],
    ['AE emirates id', fn (): bool => EmiratesId::isValid('784199000000002')],
    ['AE mobile', fn (): bool => UaeMobile::isValid('0501234567')],
    ['AE iban', fn (): bool => UaeIban::isValid('AE070331234567890123456')],
    ['SA fake national id', fn (): bool => SaudiNationalId::isValid(SaudiNationalId::fake())],
    ['SA fake citizen', fn (): bool => SaudiNationalId::isValid(SaudiNationalId::fake(IdentityType::Citizen))],
    ['SA fake mobile', fn (): bool => SaudiMobile::isValid(SaudiMobile::fake())],
    ['SA fake iban', fn (): bool => SaudiIban::isValid(SaudiIban::fake())],
    ['EG fake national id', fn (): bool => EgyptianNationalId::isValid(EgyptianNationalId::fake())],
    ['AE fake emirates id', fn (): bool => EmiratesId::isValid(EmiratesId::fake())],
    ['AE fake mobile', fn (): bool => UaeMobile::isValid(UaeMobile::fake())],
    ['AE fake iban', fn (): bool => UaeIban::isValid(UaeIban::fake())],
];

foreach ($checks as [$label, $check]) {
    if (! $check()) {
        fwrite(STDERR, "FAIL: {$label}\n");
        exit(1);
    }
}

fwrite(STDOUT, 'OK: all smoke checks passed ('.count($checks)." checks)\n");
