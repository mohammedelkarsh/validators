<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

use Validators\Ae\EmiratesId;
use Validators\Ae\UaeIban;
use Validators\Ae\UaeMobile;
use Validators\CodeIgniterAe\AeRules;
use Validators\CodeIgniterEg\EgRules;
use Validators\CodeIgniterSa\SaRules;
use Validators\Core\Normalizer;
use Validators\Eg\EgyptianNationalId;
use Validators\Eg\Gender;
use Validators\Core\Support\Iban as IbanSupport;
use Validators\Core\Support\Luhn;
use Validators\LaravelSa\Rules\SaudiIban as SaudiIbanRule;
use Validators\LaravelSa\Rules\SaudiMobile as SaudiMobileRule;
use Validators\LaravelSa\Rules\SaudiNationalId as SaudiNationalIdRule;
use Validators\Sa\IdentityType;
use Validators\Sa\SaudiIban;
use Validators\Sa\SaudiMobile;
use Validators\Sa\SaudiNationalId;

$passed = 0;
$failed = 0;
$failures = [];

function assertTrue(bool $condition, string $label): void
{
    global $passed, $failed, $failures;

    if ($condition) {
        $passed++;

        return;
    }

    $failed++;
    $failures[] = $label;
}

function assertFalse(bool $condition, string $label): void
{
    assertTrue(! $condition, $label);
}

function assertSame(mixed $expected, mixed $actual, string $label): void
{
    assertTrue($expected === $actual, $label.' (expected '.var_export($expected, true).', got '.var_export($actual, true).')');
}

function runJsonVectors(string $name, callable $validator, string $path): void
{
    $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

    foreach ($data['valid'] as $case) {
        $input = $case['input'];
        assertTrue($validator($input), "{$name} valid: {$input}");

        if (isset($case['normalized'])) {
            $result = match ($name) {
                'national_id' => SaudiNationalId::check($input),
                'mobile' => SaudiMobile::check($input),
                'iban' => SaudiIban::check($input),
                'egyptian_national_id' => EgyptianNationalId::check($input),
                'emirates_id' => EmiratesId::check($input),
                'uae_mobile' => UaeMobile::check($input),
                'uae_iban' => UaeIban::check($input),
                default => null,
            };
            assertSame($case['normalized'], $result?->normalized(), "{$name} normalized: {$input}");
        }

        if (isset($case['type'])) {
            assertSame(
                $case['type'],
                SaudiNationalId::check($input)->meta()['type'] ?? null,
                "{$name} type: {$input}"
            );
        }
    }

    foreach ($data['invalid'] as $input) {
        assertFalse($validator($input), "{$name} invalid: {$input}");
    }
}

$spec = dirname(__DIR__).'/spec/test-vectors';

runJsonVectors('national_id', SaudiNationalId::isValid(...), $spec.'/saudi-national-id.json');
runJsonVectors('mobile', SaudiMobile::isValid(...), $spec.'/saudi-mobile.json');
runJsonVectors('iban', SaudiIban::isValid(...), $spec.'/saudi-iban.json');
runJsonVectors('egyptian_national_id', EgyptianNationalId::isValid(...), $spec.'/egyptian-national-id.json');
runJsonVectors('emirates_id', EmiratesId::isValid(...), $spec.'/emirates-id.json');
runJsonVectors('uae_mobile', UaeMobile::isValid(...), $spec.'/uae-mobile.json');
runJsonVectors('uae_iban', UaeIban::isValid(...), $spec.'/uae-iban.json');

// --- SaudiNationalId extended scenarios ---
assertSame(IdentityType::Citizen, SaudiNationalId::type('1001244084'), 'type citizen enum');
assertSame(IdentityType::Resident, SaudiNationalId::type('2001244082'), 'type resident enum');
assertTrue(SaudiNationalId::type('1001244080') === null, 'type null for invalid');

assertTrue(SaudiNationalId::check('1001244084')->isValid(), 'check() returns valid result');
assertFalse(SaudiNationalId::check('1001244080')->isValid(), 'check() returns invalid result');
assertSame('1001244084', SaudiNationalId::check('1-001-244-084')->normalized(), 'national id normalized value');

assertTrue(SaudiNationalId::isValid('١٠٠١٢٤٤٠٨٤'), 'arabic-indic national id');
assertTrue(SaudiNationalId::isValid('۱۰۰۱۲۴۴۰۸۴'), 'eastern-arabic national id');
assertFalse(SaudiNationalId::isValid('0000000000'), 'all zeros national id');
assertFalse(SaudiNationalId::isValid('100124408'), '9 digits national id');
assertFalse(SaudiNationalId::isValid('10012440841'), '11 digits national id');
assertFalse(SaudiNationalId::isValid('3001244084'), 'starts with 3');
assertFalse(SaudiNationalId::isValid('0101244084'), 'starts with 0');

// Brute-force: every valid checksum ID with prefix 1 should pass
for ($serial = 0; $serial < 50; $serial++) {
    $base = '1'.str_pad((string) $serial, 8, '0', STR_PAD_LEFT);
    for ($check = 0; $check < 10; $check++) {
        $candidate = $base.$check;
        if (SaudiNationalId::passesChecksum($candidate)) {
            assertTrue(SaudiNationalId::isValid($candidate), "generated valid citizen id {$candidate}");
            break;
        }
    }
}

// --- SaudiMobile extended scenarios ---
assertSame('0501234567', SaudiMobile::normalize('050 123 4567'), 'mobile normalize spaces');
assertSame('0501234567', SaudiMobile::normalize('(05) 0123-4567'), 'mobile normalize punctuation');
assertSame('0501234567', SaudiMobile::normalize('966501234567'), 'mobile normalize 966');
assertSame('0501234567', SaudiMobile::normalize('501234567'), 'mobile normalize 9-digit');
assertSame('+966501234567', SaudiMobile::check('0501234567')->meta()['international'], 'mobile international meta');

assertTrue(SaudiMobile::isValid('0531234567'), 'STC prefix 053');
assertTrue(SaudiMobile::isValid('0541234567'), 'Mobily prefix 054');
assertTrue(SaudiMobile::isValid('0581234567'), 'Zain prefix 058');

assertFalse(SaudiMobile::isValid('+9660501234567'), 'double zero after country code');
assertFalse(SaudiMobile::isValid('0112345678'), 'landline riyadh');
assertFalse(SaudiMobile::isValid('050123456'), 'mobile too short');
assertFalse(SaudiMobile::isValid('05012345678'), 'mobile too long');
assertFalse(SaudiMobile::isValid('0601234567'), 'mobile not starting with 05');
assertFalse(SaudiMobile::isValid('abc'), 'mobile non-numeric');

// --- SaudiIban extended scenarios ---
assertTrue(SaudiIban::isValid('sa0380000000608010167519'), 'iban lowercase country');
assertSame('SA03 8000 0000 6080 1016 7519', SaudiIban::format('SA0380000000608010167519'), 'iban format');
assertSame('80', SaudiIban::check('SA0380000000608010167519')->meta()['bank_code'], 'iban bank code meta');

assertFalse(SaudiIban::isValid('SA0380000000608010167518'), 'iban wrong checksum');
assertFalse(SaudiIban::isValid('AE070331234567890123456'), 'iban wrong country only SA accepted by validator class');
assertFalse(SaudiIban::isValid('SA03'), 'iban too short');

// --- Core Normalizer ---
assertSame('0501234567', Normalizer::digitsOnly('٠٥٠١٢٣٤٥٦٧'), 'normalizer arabic-indic');
assertSame('0501234567', Normalizer::digitsOnly('۰۵۰۱۲۳۴۵۶۷'), 'normalizer eastern-arabic mixed');
assertSame('SA0380000000608010167519', Normalizer::alphanumericUpper('sa03 8000 0000 6080 1016 7519'), 'normalizer iban upper');

// --- Core Luhn / Iban support ---
assertTrue(Luhn::isValid('79927398713'), 'luhn known valid');
assertFalse(Luhn::isValid('79927398710'), 'luhn known invalid');
assertTrue(IbanSupport::isValid('SA0380000000608010167519', 'SA'), 'iban support SA');
assertFalse(IbanSupport::isValid('DE02120300000000202051', 'SA'), 'iban support rejects DE as SA');

// --- Laravel Rules ---
$laravelFail = null;
(new SaudiNationalIdRule())->validate('national_id', '1001244084', function (string $message) use (&$laravelFail): void {
    $laravelFail = $message;
});
assertTrue($laravelFail === null, 'laravel national id rule passes');

$laravelFail = null;
(new SaudiNationalIdRule())->validate('national_id', '1001244080', function (string $message) use (&$laravelFail): void {
    $laravelFail = $message;
});
assertTrue($laravelFail !== null, 'laravel national id rule fails');

$laravelFail = null;
(new SaudiMobileRule())->validate('mobile', '0501234567', function (string $message) use (&$laravelFail): void {
    $laravelFail = $message;
});
assertTrue($laravelFail === null, 'laravel mobile rule passes');

$laravelFail = null;
(new SaudiIbanRule())->validate('iban', 'SA0380000000608010167519', function (string $message) use (&$laravelFail): void {
    $laravelFail = $message;
});
assertTrue($laravelFail === null, 'laravel iban rule passes');

// --- CodeIgniter Rules ---
$ciRules = new SaRules();
$ciError = null;
assertTrue($ciRules->saudi_national_id('1001244084', $ciError), 'ci national id passes');
assertTrue($ciError === null, 'ci national id no error');

$ciError = null;
assertFalse($ciRules->saudi_national_id('1001244080', $ciError), 'ci national id fails');
assertTrue($ciError !== null, 'ci national id sets error');

$ciError = null;
assertTrue($ciRules->saudi_mobile('0501234567', $ciError), 'ci mobile passes');

$ciError = null;
assertTrue($ciRules->saudi_iban('SA0380000000608010167519', $ciError), 'ci iban passes');

// --- Egypt ---
assertTrue(EgyptianNationalId::isValid('29001011234564'), 'egyptian valid id');
assertSame('1990-01-01', EgyptianNationalId::check('29001011234564')->meta()['birth_date'], 'egyptian birth date');
assertSame(Gender::Female->value, EgyptianNationalId::check('29001011234564')->meta()['gender'], 'egyptian gender');
assertFalse(EgyptianNationalId::isValid('30213011234567'), 'egyptian invalid month');

// --- UAE ---
assertTrue(EmiratesId::isValid('784199000000002'), 'emirates id strict');
assertTrue(EmiratesId::isValid('784199000000001', strict: false), 'emirates id lenient');
assertFalse(EmiratesId::isValid('784199000000001'), 'emirates id strict rejects bad luhn');
assertTrue(UaeMobile::isValid('+971501234567'), 'uae mobile international');
assertTrue(UaeIban::isValid('AE070331234567890123456'), 'uae iban valid');

$egRules = new EgRules();
$aeRules = new AeRules();
$ciError = null;
assertTrue($egRules->egyptian_national_id('29001011234564', $ciError), 'ci egyptian id');
assertTrue($aeRules->emirates_id('784199000000002', $ciError), 'ci emirates id');
assertTrue($aeRules->uae_mobile('0501234567', $ciError), 'ci uae mobile');
assertTrue($aeRules->uae_iban('AE070331234567890123456', $ciError), 'ci uae iban');

// --- Edge: null-ish / numeric types ---
assertFalse(SaudiNationalId::isValid(null), 'null national id');
assertFalse(EgyptianNationalId::isValid(null), 'null egyptian id');
assertFalse(EmiratesId::isValid(null), 'null emirates id');
assertFalse(SaudiMobile::isValid(null), 'null mobile');
assertFalse(SaudiIban::isValid(null), 'null iban');
assertTrue(SaudiNationalId::isValid(1001244084), 'integer national id input');

// --- ValidationResult API ---
$result = SaudiNationalId::check('1001244080');
assertFalse($result->isValid(), 'result isValid false');
assertSame('sa.national_id.invalid_checksum', $result->errorKey(), 'result errorKey set');
assertTrue($result->firstError() !== null, 'result firstError english fallback');
assertTrue(count($result->errors()) > 0, 'result errors array');

echo PHP_EOL;
echo "Comprehensive test results".PHP_EOL;
echo "==========================".PHP_EOL;
echo "Passed: {$passed}".PHP_EOL;
echo "Failed: {$failed}".PHP_EOL;

if ($failures !== []) {
    echo PHP_EOL.'Failures:'.PHP_EOL;
    foreach ($failures as $failure) {
        echo "  - {$failure}".PHP_EOL;
    }
    exit(1);
}

echo PHP_EOL.'All comprehensive tests passed.'.PHP_EOL;
exit(0);
