<?php

declare(strict_types=1);

/**
 * Exhaustive scenario runner — data-driven matrix for all validators.
 * Run: php scripts/exhaustive-test.php
 */

require __DIR__.'/bootstrap.php';

use Validators\Ae\EmiratesId;
use Validators\Ae\UaeIban;
use Validators\Ae\UaeMobile;
use Validators\CodeIgniter\AeRules;
use Validators\CodeIgniter\EgRules;
use Validators\CodeIgniter\SaRules;
use Validators\Core\Normalizer;
use Validators\Core\Support\Iban as IbanSupport;
use Validators\Core\Support\Luhn;
use Validators\Core\ValidationResult;
use Validators\Eg\EgyptianNationalId;
use Validators\Eg\Gender;
use Validators\Eg\Governorates;
use Validators\Laravel\Rules\EgyptianNationalId as EgyptianNationalIdRule;
use Validators\Laravel\Rules\EmiratesId as EmiratesIdRule;
use Validators\Laravel\Rules\SaudiIban as SaudiIbanRule;
use Validators\Laravel\Rules\SaudiMobile as SaudiMobileRule;
use Validators\Laravel\Rules\SaudiNationalId as SaudiNationalIdRule;
use Validators\Laravel\Rules\UaeIban as UaeIbanRule;
use Validators\Laravel\Rules\UaeMobile as UaeMobileRule;
use Validators\Sa\IdentityType;
use Validators\Sa\SaudiIban;
use Validators\Sa\SaudiMobile;
use Validators\Sa\SaudiNationalId;

// ---------------------------------------------------------------------------
// Harness
// ---------------------------------------------------------------------------

$stats = ['passed' => 0, 'failed' => 0, 'groups' => []];
$failures = [];

function scenario(string $group, string $name, bool $ok): void
{
    global $stats, $failures;

    $stats['groups'][$group] ??= ['passed' => 0, 'failed' => 0];
    $stats['groups'][$group][$ok ? 'passed' : 'failed']++;
    $stats[$ok ? 'passed' : 'failed']++;

    if (! $ok) {
        $failures[] = "[{$group}] {$name}";
    }
}

function expectValid(string $group, string $name, callable $validator, mixed $input): void
{
    scenario($group, $name, (bool) $validator($input));
}

function expectInvalid(string $group, string $name, callable $validator, mixed $input): void
{
    scenario($group, $name, ! (bool) $validator($input));
}

function expectSame(string $group, string $name, mixed $expected, mixed $actual): void
{
    scenario($group, $name, $expected === $actual);
}

function laravelRulePasses(object $rule, mixed $value): bool
{
    $failed = false;

    $rule->validate('field', $value, function () use (&$failed): void {
        $failed = true;
    });

    return ! $failed;
}

// ---------------------------------------------------------------------------
// Generators
// ---------------------------------------------------------------------------

function saudiIdWithPrefix(int $prefix, int $serial): ?string
{
    $base = (string) $prefix.str_pad((string) $serial, 8, '0', STR_PAD_LEFT);

    for ($check = 0; $check < 10; $check++) {
        $candidate = $base.$check;

        if (SaudiNationalId::passesChecksum($candidate)) {
            return $candidate;
        }
    }

    return null;
}

function egyptianId(string $firstThirteen): string
{
    return $firstThirteen.EgyptianNationalId::calculateCheckDigit($firstThirteen);
}

function emiratesIdWithBody(string $twelveDigitsAfter784): ?string
{
    $prefix = '784'.$twelveDigitsAfter784;

    if (strlen($prefix) !== 14) {
        return null;
    }

    for ($d = 0; $d < 10; $d++) {
        $candidate = $prefix.$d;

        if (Luhn::isValid($candidate)) {
            return $candidate;
        }
    }

    return null;
}

// ---------------------------------------------------------------------------
// 1. CORE
// ---------------------------------------------------------------------------

$group = 'core.normalizer';

expectSame($group, 'arabic-indic digits', '0501234567', Normalizer::digitsOnly('٠٥٠١٢٣٤٥٦٧'));
expectSame($group, 'eastern-arabic digits', '0501234567', Normalizer::digitsOnly('۰۵۰۱۲۳۴۵۶۷'));
expectSame($group, 'strip punctuation', '1001244084', Normalizer::digitsOnly('1-001.244/084'));
expectSame($group, 'iban upper + spaces', 'SA0380000000608010167519', Normalizer::alphanumericUpper('sa03 8000 0000 6080 1016 7519'));
expectSame($group, 'empty string', '', Normalizer::digitsOnly(''));
expectSame($group, 'letters only', '', Normalizer::digitsOnly('abc'));

$group = 'core.luhn';
expectValid($group, 'known valid', Luhn::isValid(...), '79927398713');
expectInvalid($group, 'known invalid', Luhn::isValid(...), '79927398710');
expectInvalid($group, 'empty', Luhn::isValid(...), '');
expectInvalid($group, 'letters', Luhn::isValid(...), '7992739871a');

$group = 'core.iban';
expectValid($group, 'SA valid', fn ($v) => IbanSupport::isValid($v, 'SA'), 'SA0380000000608010167519');
expectValid($group, 'AE valid', fn ($v) => IbanSupport::isValid($v, 'AE'), 'AE070331234567890123456');
expectInvalid($group, 'SA rejects DE', fn ($v) => IbanSupport::isValid($v, 'SA'), 'DE02120300000000202051');
expectInvalid($group, 'bad checksum', fn ($v) => IbanSupport::isValid($v, 'SA'), 'SA0380000000608010167518');

// ---------------------------------------------------------------------------
// 2. SAUDI — National ID
// ---------------------------------------------------------------------------

$group = 'sa.national_id.valid';

$citizenId = saudiIdWithPrefix(1, 1244) ?? '1001244084';
$residentId = saudiIdWithPrefix(2, 1244) ?? '2001244082';

expectValid($group, 'citizen plain', SaudiNationalId::isValid(...), $citizenId);
expectValid($group, 'resident plain', SaudiNationalId::isValid(...), $residentId);
expectValid($group, 'with spaces', SaudiNationalId::isValid(...), '1 001 244 084');
expectValid($group, 'arabic-indic', SaudiNationalId::isValid(...), '١٠٠١٢٤٤٠٨٤');
expectValid($group, 'integer input', SaudiNationalId::isValid(...), (int) $citizenId);
expectSame($group, 'type citizen', IdentityType::Citizen, SaudiNationalId::type($citizenId));
expectSame($group, 'type resident', IdentityType::Resident, SaudiNationalId::type($residentId));

for ($serial = 0; $serial < 100; $serial++) {
    $generated = saudiIdWithPrefix(1, $serial);

    if ($generated !== null) {
        expectValid($group, "generated citizen #{$serial}", SaudiNationalId::isValid(...), $generated);
    }
}

$group = 'sa.national_id.invalid';

expectInvalid($group, 'empty', SaudiNationalId::isValid(...), '');
expectInvalid($group, 'null', SaudiNationalId::isValid(...), null);
expectInvalid($group, 'whitespace', SaudiNationalId::isValid(...), '   ');
expectInvalid($group, '9 digits', SaudiNationalId::isValid(...), '100124408');
expectInvalid($group, '11 digits', SaudiNationalId::isValid(...), '10012440841');
expectInvalid($group, 'starts with 0', SaudiNationalId::isValid(...), '0101244084');
expectInvalid($group, 'starts with 3', SaudiNationalId::isValid(...), '3001244084');
expectInvalid($group, 'starts with 9', SaudiNationalId::isValid(...), '9001244084');
expectInvalid($group, 'bad checksum', SaudiNationalId::isValid(...), '1001244080');
expectInvalid($group, 'letters', SaudiNationalId::isValid(...), '100124408a');
expectInvalid($group, 'all zeros', SaudiNationalId::isValid(...), '0000000000');
expectSame($group, 'type null on invalid', null, SaudiNationalId::type('1001244080'));

$result = SaudiNationalId::check('1001244080');
scenario($group, 'result has errors', $result instanceof ValidationResult && ! $result->isValid() && $result->firstError() !== null);

// ---------------------------------------------------------------------------
// 3. SAUDI — Mobile
// ---------------------------------------------------------------------------

$group = 'sa.mobile.valid';

foreach (['050', '051', '053', '054', '055', '056', '058', '059'] as $prefix) {
    expectValid($group, "prefix {$prefix}", SaudiMobile::isValid(...), $prefix.'1234567');
}

expectValid($group, 'local plain', SaudiMobile::isValid(...), '0501234567');
expectValid($group, '+966 format', SaudiMobile::isValid(...), '+966501234567');
expectValid($group, '966 no plus', SaudiMobile::isValid(...), '966501234567');
expectValid($group, '9-digit no zero', SaudiMobile::isValid(...), '501234567');
expectValid($group, 'with spaces', SaudiMobile::isValid(...), '050 123 4567');
expectValid($group, 'with parens', SaudiMobile::isValid(...), '(05) 0123-4567');
expectValid($group, 'arabic digits', SaudiMobile::isValid(...), '٠٥٠١٢٣٤٥٦٧');
expectSame($group, 'international meta', '+966501234567', SaudiMobile::check('0501234567')->meta()['international']);

$group = 'sa.mobile.invalid';

expectInvalid($group, 'empty', SaudiMobile::isValid(...), '');
expectInvalid($group, 'null', SaudiMobile::isValid(...), null);
expectInvalid($group, '+966050 double zero', SaudiMobile::isValid(...), '+9660501234567');
expectInvalid($group, 'landline 011', SaudiMobile::isValid(...), '0112345678');
expectInvalid($group, 'landline 012', SaudiMobile::isValid(...), '0123456789');
expectInvalid($group, 'landline 013', SaudiMobile::isValid(...), '0134567890');
expectInvalid($group, 'too short', SaudiMobile::isValid(...), '050123456');
expectInvalid($group, 'too long', SaudiMobile::isValid(...), '05012345678');
expectInvalid($group, 'starts 06', SaudiMobile::isValid(...), '0601234567');
expectInvalid($group, 'starts 04', SaudiMobile::isValid(...), '0401234567');
expectInvalid($group, 'letters', SaudiMobile::isValid(...), '050123456a');

// ---------------------------------------------------------------------------
// 4. SAUDI — IBAN
// ---------------------------------------------------------------------------

$group = 'sa.iban.valid';

expectValid($group, 'known valid', SaudiIban::isValid(...), 'SA0380000000608010167519');
expectValid($group, 'lowercase', SaudiIban::isValid(...), 'sa0380000000608010167519');
expectValid($group, 'with spaces', SaudiIban::isValid(...), 'SA03 8000 0000 6080 1016 7519');
expectSame($group, 'format display', 'SA03 8000 0000 6080 1016 7519', SaudiIban::format('SA0380000000608010167519'));
expectSame($group, 'bank code meta', '80', SaudiIban::check('SA0380000000608010167519')->meta()['bank_code']);

$group = 'sa.iban.invalid';

expectInvalid($group, 'empty', SaudiIban::isValid(...), '');
expectInvalid($group, 'null', SaudiIban::isValid(...), null);
expectInvalid($group, 'wrong checksum', SaudiIban::isValid(...), 'SA0380000000608010167518');
expectInvalid($group, 'too short', SaudiIban::isValid(...), 'SA038000000060801016751');
expectInvalid($group, 'too long', SaudiIban::isValid(...), 'SA03800000006080101675190');
expectInvalid($group, 'wrong country AE', SaudiIban::isValid(...), 'AE070331234567890123456');
expectInvalid($group, 'special chars', SaudiIban::isValid(...), 'SA038000000060801016751!');

// ---------------------------------------------------------------------------
// 5. EGYPT — National ID
// ---------------------------------------------------------------------------

$group = 'eg.national_id.valid';

$egFemale = egyptianId('2900101123456');
$egMale = egyptianId('2900101123451');
$eg2000s = egyptianId('3050615123451');
$egForeign = egyptianId('2900101188121');
$egLeap = egyptianId('3000229123451');

expectValid($group, 'female dob 1990-01-01', EgyptianNationalId::isValid(...), $egFemale);
expectValid($group, 'male', EgyptianNationalId::isValid(...), $egMale);
expectValid($group, 'century 3 (2000s)', EgyptianNationalId::isValid(...), $eg2000s);
expectValid($group, 'governorate foreign 88', EgyptianNationalId::isValid(...), $egForeign);
expectValid($group, 'leap day 2000-02-29', EgyptianNationalId::isValid(...), $egLeap);
expectValid($group, 'with spaces', EgyptianNationalId::isValid(...), '2 900 101 123 456 4');
expectValid($group, 'arabic digits', EgyptianNationalId::isValid(...), '٢٩٠٠١٠١١٢٣٤٥٦٤');

$r = EgyptianNationalId::check($egFemale);
expectSame($group, 'birth_date meta', '1990-01-01', $r->meta()['birth_date']);
expectSame($group, 'gender female', Gender::Female->value, $r->meta()['gender']);
expectSame($group, 'governorate dakahlia', 'Dakahlia', $r->meta()['governorate']);
expectSame($group, 'governorate code 12', 12, $r->meta()['governorate_code']);

$rMale = EgyptianNationalId::check($egMale);
expectSame($group, 'gender male', Gender::Male->value, $rMale->meta()['gender']);

foreach ([1, 2, 12, 21, 27, 88] as $govCode) {
    $base = sprintf('2900101%02d3451', $govCode);
    $id = egyptianId($base);
    expectValid($group, "governorate {$govCode}", EgyptianNationalId::isValid(...), $id);
    expectSame($group, "governorate name {$govCode}", Governorates::name($govCode), EgyptianNationalId::check($id)->meta()['governorate']);
}

$group = 'eg.national_id.invalid';

expectInvalid($group, 'empty', EgyptianNationalId::isValid(...), '');
expectInvalid($group, 'null', EgyptianNationalId::isValid(...), null);
expectInvalid($group, 'too short', EgyptianNationalId::isValid(...), '290010112345');
expectInvalid($group, 'too long', EgyptianNationalId::isValid(...), '290010112345641');
expectInvalid($group, 'century 1', EgyptianNationalId::isValid(...), '19001011234567');
expectInvalid($group, 'century 4', EgyptianNationalId::isValid(...), '49001011234567');
expectInvalid($group, 'month 00', EgyptianNationalId::isValid(...), egyptianId('2900001123456'));
expectInvalid($group, 'month 13', EgyptianNationalId::isValid(...), egyptianId('2913011123456'));
expectInvalid($group, 'day 00', EgyptianNationalId::isValid(...), egyptianId('2900100123456'));
expectInvalid($group, 'day 32', EgyptianNationalId::isValid(...), egyptianId('2901321123456'));
expectInvalid($group, 'feb 30', EgyptianNationalId::isValid(...), egyptianId('2902301123456'));
expectInvalid($group, 'feb 29 non-leap', EgyptianNationalId::isValid(...), egyptianId('2902291123456'));
expectInvalid($group, 'future birth date', EgyptianNationalId::isValid(...), egyptianId('3280101123451'));
expectInvalid($group, 'invalid governorate 99', EgyptianNationalId::isValid(...), egyptianId('2900101993456'));
expectInvalid($group, 'bad checksum', EgyptianNationalId::isValid(...), '29001011234560');
expectInvalid($group, 'letters', EgyptianNationalId::isValid(...), '2900101123456a');

// ---------------------------------------------------------------------------
// 6. UAE — Emirates ID
// ---------------------------------------------------------------------------

$group = 'ae.emirates_id.valid';

$eid = emiratesIdWithBody('199000000000') ?? '784199000000002';

expectValid($group, 'strict luhn valid', EmiratesId::isValid(...), $eid);
expectValid($group, 'with dashes', EmiratesId::isValid(...), '784-1990-0000000-2');
expectValid($group, 'lenient format only', fn ($v) => EmiratesId::isValid($v, false), '784199000000001');
expectSame($group, 'format display', '784-1990-0000000-2', EmiratesId::format($eid));
expectSame($group, 'registration year meta', '1990', EmiratesId::check($eid)->meta()['registration_year']);

$group = 'ae.emirates_id.invalid';

expectInvalid($group, 'empty', EmiratesId::isValid(...), '');
expectInvalid($group, 'null', EmiratesId::isValid(...), null);
expectInvalid($group, 'wrong prefix 885', EmiratesId::isValid(...), '885199000000002');
expectInvalid($group, 'too short', EmiratesId::isValid(...), '78419900000000');
expectInvalid($group, 'too long', EmiratesId::isValid(...), '7841990000000022');
expectInvalid($group, 'strict bad luhn', EmiratesId::isValid(...), '784199000000001');
expectInvalid($group, 'letters', EmiratesId::isValid(...), '78419900000000a');

// ---------------------------------------------------------------------------
// 7. UAE — Mobile
// ---------------------------------------------------------------------------

$group = 'ae.mobile.valid';

foreach (['050', '052', '054', '055', '056', '058'] as $prefix) {
    expectValid($group, "prefix {$prefix}", UaeMobile::isValid(...), $prefix.'1234567');
}

expectValid($group, '+971 format', UaeMobile::isValid(...), '+971501234567');
expectValid($group, '971 no plus', UaeMobile::isValid(...), '971501234567');
expectValid($group, '9-digit', UaeMobile::isValid(...), '501234567');
expectValid($group, 'arabic digits', UaeMobile::isValid(...), '٠٥٠١٢٣٤٥٦٧');
expectSame($group, 'international meta', '+971501234567', UaeMobile::check('0501234567')->meta()['international']);

$group = 'ae.mobile.invalid';

expectInvalid($group, 'empty', UaeMobile::isValid(...), '');
expectInvalid($group, '+971050 double zero', UaeMobile::isValid(...), '+9710501234567');
expectInvalid($group, 'landline 02', UaeMobile::isValid(...), '0212345678');
expectInvalid($group, 'too short', UaeMobile::isValid(...), '050123456');
expectInvalid($group, 'too long', UaeMobile::isValid(...), '05012345678');
expectInvalid($group, 'starts 06', UaeMobile::isValid(...), '0601234567');

// ---------------------------------------------------------------------------
// 8. UAE — IBAN
// ---------------------------------------------------------------------------

$group = 'ae.iban.valid';

expectValid($group, 'known valid', UaeIban::isValid(...), 'AE070331234567890123456');
expectValid($group, 'lowercase', UaeIban::isValid(...), 'ae070331234567890123456');
expectValid($group, 'with spaces', UaeIban::isValid(...), 'AE07 0331 2345 6789 0123 456');
expectSame($group, 'bank code meta', '033', UaeIban::check('AE070331234567890123456')->meta()['bank_code']);

$group = 'ae.iban.invalid';

expectInvalid($group, 'empty', UaeIban::isValid(...), '');
expectInvalid($group, 'wrong checksum', UaeIban::isValid(...), 'AE070331234567890123450');
expectInvalid($group, 'too short', UaeIban::isValid(...), 'AE07033123456789012345');
expectInvalid($group, 'SA iban rejected', UaeIban::isValid(...), 'SA0380000000608010167519');

// ---------------------------------------------------------------------------
// 9. LARAVEL RULES (all countries)
// ---------------------------------------------------------------------------

$group = 'laravel.rules';

expectValid($group, 'saudi national id', fn ($v) => laravelRulePasses(new SaudiNationalIdRule(), $v), $citizenId);
expectInvalid($group, 'saudi national id fail', fn ($v) => laravelRulePasses(new SaudiNationalIdRule(), $v), '1001244080');
expectValid($group, 'saudi mobile', fn ($v) => laravelRulePasses(new SaudiMobileRule(), $v), '0501234567');
expectValid($group, 'saudi iban', fn ($v) => laravelRulePasses(new SaudiIbanRule(), $v), 'SA0380000000608010167519');
expectValid($group, 'egyptian national id', fn ($v) => laravelRulePasses(new EgyptianNationalIdRule(), $v), $egFemale);
expectInvalid($group, 'egyptian national id fail', fn ($v) => laravelRulePasses(new EgyptianNationalIdRule(), $v), '29001011234560');
expectValid($group, 'emirates id', fn ($v) => laravelRulePasses(new EmiratesIdRule(), $v), $eid);
expectInvalid($group, 'emirates id fail', fn ($v) => laravelRulePasses(new EmiratesIdRule(), $v), '784199000000001');
expectValid($group, 'uae mobile', fn ($v) => laravelRulePasses(new UaeMobileRule(), $v), '0501234567');
expectValid($group, 'uae iban', fn ($v) => laravelRulePasses(new UaeIbanRule(), $v), 'AE070331234567890123456');

// ---------------------------------------------------------------------------
// 10. CODEIGNITER RULES (all countries + error messages)
// ---------------------------------------------------------------------------

$group = 'codeigniter.rules';

$sa = new SaRules();
$eg = new EgRules();
$ae = new AeRules();
$error = null;

scenario($group, 'sa id pass', $sa->saudi_national_id($citizenId, $error) && $error === null);
$error = 'preset';
scenario($group, 'sa id fail sets error', ! $sa->saudi_national_id('1001244080', $error) && is_string($error) && $error !== 'preset');
$error = null;
scenario($group, 'sa mobile pass', $sa->saudi_mobile('0501234567', $error));
$error = null;
scenario($group, 'sa iban pass', $sa->saudi_iban('SA0380000000608010167519', $error));

$error = null;
scenario($group, 'eg id pass', $eg->egyptian_national_id($egFemale, $error) && $error === null);
$error = null;
scenario($group, 'eg id fail', ! $eg->egyptian_national_id('29001011234560', $error) && $error !== null);

$error = null;
scenario($group, 'ae emirates pass', $ae->emirates_id($eid, $error));
$error = null;
scenario($group, 'ae mobile pass', $ae->uae_mobile('0501234567', $error));
$error = null;
scenario($group, 'ae iban pass', $ae->uae_iban('AE070331234567890123456', $error));
$error = null;
scenario($group, 'ae emirates fail', ! $ae->emirates_id('784199000000001', $error) && $error !== null);

// ---------------------------------------------------------------------------
// 11. JSON test vectors (spec files)
// ---------------------------------------------------------------------------

$group = 'spec.json_vectors';
$spec = dirname(__DIR__).'/spec/test-vectors';
$vectorFiles = glob($spec.'/*.json') ?: [];

foreach ($vectorFiles as $file) {
    $name = basename($file, '.json');
    $data = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

    $callable = match ($name) {
        'saudi-national-id' => SaudiNationalId::isValid(...),
        'saudi-mobile' => SaudiMobile::isValid(...),
        'saudi-iban' => SaudiIban::isValid(...),
        'egyptian-national-id' => EgyptianNationalId::isValid(...),
        'emirates-id' => EmiratesId::isValid(...),
        'uae-mobile' => UaeMobile::isValid(...),
        'uae-iban' => UaeIban::isValid(...),
        default => null,
    };

    if ($callable === null) {
        continue;
    }

    foreach ($data['valid'] as $case) {
        expectValid($group, "{$name} valid: {$case['input']}", $callable, $case['input']);
    }

    foreach ($data['invalid'] as $input) {
        expectInvalid($group, "{$name} invalid: {$input}", $callable, $input);
    }
}

// ---------------------------------------------------------------------------
// Report
// ---------------------------------------------------------------------------

echo PHP_EOL.'EXHAUSTIVE TEST REPORT'.PHP_EOL;
echo str_repeat('=', 60).PHP_EOL;
echo 'Total passed : '.$stats['passed'].PHP_EOL;
echo 'Total failed : '.$stats['failed'].PHP_EOL;
echo PHP_EOL.'By group:'.PHP_EOL;

foreach ($stats['groups'] as $name => $counts) {
    $status = $counts['failed'] === 0 ? 'OK' : 'FAIL';
    echo sprintf("  [%s] %s — passed %d, failed %d\n", $status, $name, $counts['passed'], $counts['failed']);
}

if ($failures !== []) {
    echo PHP_EOL.'Failures:'.PHP_EOL;

    foreach ($failures as $failure) {
        echo "  - {$failure}".PHP_EOL;
    }

    exit(1);
}

echo PHP_EOL.'All exhaustive scenarios passed.'.PHP_EOL;
exit(0);
