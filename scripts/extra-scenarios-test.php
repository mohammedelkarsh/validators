<?php

declare(strict_types=1);

/**
 * Additional scenarios not covered in exhaustive-test.php.
 */

require __DIR__.'/bootstrap.php';

use Validators\Ae\EmiratesId;
use Validators\Ae\UaeIban;
use Validators\Ae\UaeMobile;
use Validators\CodeIgniter\AeRules;
use Validators\CodeIgniter\EgRules;
use Validators\CodeIgniter\SaRules;
use Validators\Eg\EgyptianNationalId;
use Validators\Eg\Governorates;
use Validators\Laravel\Rules\EgyptianNationalId as EgRule;
use Validators\Laravel\Rules\EmiratesId as AeIdRule;
use Validators\Laravel\Rules\SaudiNationalId as SaIdRule;
use Validators\Laravel\Rules\SaudiIban as SaIbanRule;
use Validators\Sa\SaudiIban;
use Validators\Sa\SaudiMobile;
use Validators\Sa\SaudiNationalId;

$passed = 0;
$failed = 0;
$failures = [];

function check(bool $ok, string $label): void
{
    global $passed, $failed, $failures;

    if ($ok) {
        $passed++;

        return;
    }

    $failed++;
    $failures[] = $label;
}

function egyptianId(string $first13): string
{
    return $first13.EgyptianNationalId::calculateCheckDigit($first13);
}

function saudiId(int $prefix, int $serial): ?string
{
    $base = (string) $prefix.str_pad((string) $serial, 8, '0', STR_PAD_LEFT);

    for ($digit = 0; $digit < 10; $digit++) {
        $candidate = $base.$digit;

        if (SaudiNationalId::passesChecksum($candidate)) {
            return $candidate;
        }
    }

    return null;
}

// Odd input types
check(! SaudiNationalId::isValid(false), 'SA national id rejects false');
check(! SaudiNationalId::isValid(true), 'SA national id rejects true');
check(! SaudiNationalId::isValid(1001244084.5), 'SA national id rejects float');
check(! SaudiNationalId::isValid(new stdClass()), 'SA national id rejects object');
check(! SaudiNationalId::isValid('   '), 'SA national id rejects spaces only');
check(! SaudiNationalId::isValid("\t\n"), 'SA national id rejects whitespace');

check(EgyptianNationalId::isValid((int) egyptianId('2900101123456')), 'EG national id accepts integer');
check(! EgyptianNationalId::isValid(false), 'EG national id rejects false');

// Mixed Arabic/Latin digits
check(SaudiMobile::isValid('05٠١٢34567'), 'SA mobile mixed digit scripts');
check(SaudiNationalId::isValid('1٠٠١٢٤4٠84'), 'SA national id mixed digit scripts');

// Whitespace in formatted values
check(SaudiIban::isValid("SA03\n8000\t0000 6080 1016 7519"), 'SA iban with newline and tab');
check(UaeIban::isValid("AE07\n0331 2345 6789 0123 456"), 'AE iban with newline');
check(EmiratesId::isValid("784\u{2010}1990\u{2010}0000000\u{2010}2"), 'AE emirates id unicode dash');

// Format helpers with invalid length
check(EmiratesId::format('123') === '123', 'AE format returns input when length invalid');
check(SaudiIban::format('SA03') === 'SA03', 'SA iban format returns short input unchanged');

// Generate 100 resident ids
for ($index = 0; $index < 100; $index++) {
    $id = saudiId(2, $index);

    if ($id !== null) {
        check(SaudiNationalId::isValid($id), "generated resident id #{$index}");
    }
}

// All Egyptian governorates
$governorateCodes = Governorates::codes();

foreach ($governorateCodes as $code) {
    $base = sprintf('2900101%02d3451', $code);
    $id = egyptianId($base);
    check(EgyptianNationalId::isValid($id), "EG governorate code {$code}");
    check(Governorates::name($code) !== null, "EG governorate name for {$code}");
}

// Egyptian boundary dates
check(EgyptianNationalId::isValid(egyptianId('2991231123451')), 'EG last day of 1999');
check(EgyptianNationalId::isValid(egyptianId('3000101123451')), 'EG first day of 2000');
check(! EgyptianNationalId::isValid(egyptianId('2904311123451')), 'EG april 31 rejected');
check(! EgyptianNationalId::isValid(egyptianId('2902291123456')), 'EG feb 29 1990 non-leap rejected');

// Cross-country rejection
check(! SaudiNationalId::isValid('29001011234564'), 'EG id rejected by SA validator');
check(! EgyptianNationalId::isValid('1001244084'), 'SA id rejected by EG validator');
check(! EmiratesId::isValid('1001244084'), 'SA id rejected by AE validator');
check(! SaudiNationalId::isValid('784199000000002'), 'AE id rejected by SA validator');

// Service numbers rejected
check(! SaudiMobile::isValid('0800111234'), 'SA 800 toll number rejected');
check(! SaudiMobile::isValid('0920123456'), 'SA 920 service number rejected');
check(! UaeMobile::isValid('0212345678'), 'AE dubai landline 02 rejected');
check(! UaeMobile::isValid('0712345678'), 'AE abu dhabi landline 07 rejected');

// IBAN letters in body
check(! SaudiIban::isValid('SA03ABCDEF0608010167519'), 'SA iban letters in body rejected');
check(! UaeIban::isValid('AE07ABCDEF67890123456'), 'AE iban letters in body rejected');

// Direct checksum helpers
check(! SaudiNationalId::passesChecksum('123'), 'SA checksum rejects length 3');
check(! SaudiNationalId::passesChecksum('abcdefghij'), 'SA checksum rejects letters');

// Laravel rules: empty values
$fail = false;
(new SaIdRule())->validate('field', '', function () use (&$fail): void { $fail = true; });
check($fail, 'laravel SA national id empty fails');

$fail = false;
(new EgRule())->validate('field', '', function () use (&$fail): void { $fail = true; });
check($fail, 'laravel EG national id empty fails');

$fail = false;
(new AeIdRule())->validate('field', '', function () use (&$fail): void { $fail = true; });
check($fail, 'laravel AE emirates id empty fails');

$fail = false;
(new SaIbanRule())->validate('field', '', function () use (&$fail): void { $fail = true; });
check($fail, 'laravel SA iban empty fails');

// CodeIgniter: empty values and error keys
$sa = new SaRules();
$eg = new EgRules();
$ae = new AeRules();
$error = null;

check(! $sa->saudi_national_id('', $error) && $error === 'sa.national_id.required', 'CI SA national id empty returns key');
check(! $eg->egyptian_national_id(null, $error) && $error === 'eg.national_id.required', 'CI EG national id null returns key');
check(! $ae->emirates_id('', $error) && $error === 'ae.emirates_id.required', 'CI AE emirates id empty returns key');
check(! $ae->uae_mobile(null, $error) && $error === 'ae.mobile.required', 'CI AE mobile null returns key');
check(! $ae->uae_iban('', $error) && $error === 'ae.iban.required', 'CI AE iban empty returns key');

// ValidationResult fields
$result = SaudiNationalId::check('1001244080');
check($result->normalized() === '1001244080', 'normalized value preserved on failure');
check(count($result->meta()) === 0, 'no metadata on failure');

$result = EmiratesId::check('784199000000002', false);
check($result->meta()['strict'] === false, 'lenient mode stored in metadata');

// Oversized input
check(! SaudiNationalId::isValid('1001244084'.str_repeat('0', 50)), 'SA national id oversized rejected');
check(! EgyptianNationalId::isValid(egyptianId('2900101123456').'000'), 'EG national id oversized rejected');

// fake() batch
for ($index = 0; $index < 5; $index++) {
    check(SaudiNationalId::isValid(SaudiNationalId::fake()), 'SA fake national id');
    check(EgyptianNationalId::isValid(EgyptianNationalId::fake()), 'EG fake national id');
    check(EmiratesId::isValid(EmiratesId::fake()), 'AE fake emirates id');
}

echo PHP_EOL.'Extra scenarios test'.PHP_EOL;
echo str_repeat('=', 40).PHP_EOL;
echo "Passed: {$passed}".PHP_EOL;
echo "Failed: {$failed}".PHP_EOL;

if ($failures !== []) {
    echo PHP_EOL.'Failures:'.PHP_EOL;

    foreach ($failures as $failure) {
        echo "  - {$failure}".PHP_EOL;
    }

    exit(1);
}

echo PHP_EOL.'All extra scenarios passed.'.PHP_EOL;
